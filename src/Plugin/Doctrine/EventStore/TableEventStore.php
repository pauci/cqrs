<?php

namespace CQRS\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\GenericEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\EventStore\EventStoreInterface;
use CQRS\Serializer\SerializerInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use PDO;
use Rhumsaa\Uuid\Uuid;

class TableEventStore implements EventStoreInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $table = 'cqrs_event';

    /**
     * @param SerializerInterface $serializer
     * @param Connection $connection
     * @param string $table
     */
    public function __construct(SerializerInterface $serializer, Connection $connection, $table = null)
    {
        $this->serializer = $serializer;
        $this->connection = $connection;

        if (null !== $table) {
            $this->table = $table;
        }
    }

    /**
     * @param EventMessageInterface $event
     */
    public function store(EventMessageInterface $event)
    {
        $data = $this->toArray($event);
        $this->connection->insert($this->table, $data);
    }

    /**.
     * @param int|null $offset
     * @param int $limit
     * @return EventMessageInterface[]
     */
    public function read($offset = null, $limit = 10)
    {
        if ($offset === null) {
            $offset = (((int) (($this->getLastId() - 1) / $limit)) * $limit) + 1;
        }

        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE id >= ?'
            . ' ORDER BY id ASC'
            . ' LIMIT ?';

        $events = [];

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $offset, Type::INTEGER);
        $stmt->bindValue(2, $limit, Type::INTEGER);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $events[$row['id']] = $this->fromArray($row);
        }

        return $events;
    }

    /**
     * @param EventMessageInterface $event
     * @return array
     */
    private function toArray(EventMessageInterface $event)
    {
        $data = [
            'event_id'     => (string) $event->getId(),
            'event_date'   => $event->getTimestamp()->format('Y-m-d H:i:s'),
            'event_date_u' => $event->getTimestamp()->format('u'),
            'payload_type' => $event->getPayloadType(),
            'payload'      => $this->serializer->serialize($event->getPayload()),
            'metadata'     => $this->serializer->serialize($event->getMetadata()),
        ];

        if ($event instanceof DomainEventMessageInterface) {
            $aggregateId = $event->getAggregateId();
            if ($aggregateId instanceof Uuid) {
                $aggregateId = (string) $aggregateId;
            } else {
                $aggregateId = json_encode($aggregateId);
            }

            $data = array_merge($data, [
                'aggregate_type'  => $event->getAggregateType(),
                'aggregate_id'    => $aggregateId,
                'sequence_number' => $event->getSequenceNumber(),
            ]);
        }

        return $data;
    }

    /**
     * @param array $data
     * @return GenericDomainEventMessage|GenericEventMessage
     */
    public function fromArray(array $data)
    {
        $payload   = $this->serializer->deserialize($data['payload'], $data['payload_type']);
        /** @var Metadata $metadata */
        $metadata  = $this->serializer->deserialize($data['metadata'], Metadata::class);
        $id        = Uuid::fromString($data['event_id']);
        $timestamp = DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', "{$data['event_date']}.{$data['event_date_u']}");

        if (isset($data['aggregate_type'])) {
            return new GenericDomainEventMessage(
                $data['aggregate_type'],
                $data['aggregate_id'],
                $data['sequence_number'],
                $payload,
                $metadata,
                $id,
                $timestamp
            );
        }

        return new GenericEventMessage($payload, $metadata, $id, $timestamp);
    }

    /**
     * @return int
     */
    private function getLastId()
    {
        $sql = 'SELECT MAX(id) FROM ' . $this->table;

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn() ?: 1;
    }
}
