<?php

namespace CQRS\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\EventHandling\EventInterface;
use CQRS\EventStore\EventStoreInterface;
use CQRS\Serializer\SerializerInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Rhumsaa\Uuid\Uuid;

class TableEventStore implements EventStoreInterface
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var Connection */
    private $connection;

    /** @var string */
    private $table;

    /**
     * @param SerializerInterface $serializer
     * @param Connection $connection
     * @param string $table
     */
    public function __construct(SerializerInterface $serializer, Connection $connection, $table = 'cqrs_event')
    {
        $this->serializer = $serializer;
        $this->connection = $connection;
        $this->table      = $table;
    }

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param EventMessageInterface $event
     */
    public function store(EventMessageInterface $event)
    {
        $data = [
            'event_id'        => (string) $event->getId(),
            'event_date'      => $event->getTimestamp()->format('Y-m-d H:i:s'),
            'event_date_u'    => $event->getTimestamp()->format('u'),
            'payload_type'    => $event->getPayloadType(),
            'payload'         => $this->serializer->serialize($event->getPayload(), 'json'),
            'metadata'        => $this->serializer->serialize($event->getMetadata(), 'json'),
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

        $this->connection->insert($this->table, $data);
    }

    /**.
     * @param int|null $offset
     * @param int $limit
     * @return GenericDomainEventMessage[]
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

        $stmt = $this->connection->executeQuery($sql, [$offset, $limit]);

        foreach ($stmt as $row) {
            /** @var EventInterface $payload */
            $payload  = $this->serializer->deserialize($row['payload'], $row['payload_type'], 'json');
            $metadata = $this->serializer->deserialize($row['metadata'], Metadata::class, 'json');

            $events[$row['id']] = new GenericDomainEventMessage(
                $row['aggregate_type'],
                $row['aggregate_id'],
                $row['sequence_number'],
                $payload,
                $metadata,
                Uuid::fromString($row['event_id']),
                DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', "{$row['event_date']}.{$row['event_date_u']}")
            );
        }

        return $events;
    }

    /**
     * @return int
     */
    private function getLastId()
    {
        $sql = 'SELECT MAX(id) FROM ' . $this->table;

        $maxId = $this->connection->executeQuery($sql)
            ->fetchColumn();

        return $maxId ?: 1;
    }
}
