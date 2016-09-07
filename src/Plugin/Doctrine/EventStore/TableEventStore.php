<?php

namespace CQRS\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\GenericEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\EventStore\EventStoreInterface;
use CQRS\Exception\OutOfBoundsException;
use CQRS\Serializer\SerializerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Generator;
use Pauci\DateTime\DateTime;
use PDO;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

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
            $offset = (((int) (($this->getLastRowId() - 1) / $limit)) * $limit) + 1;
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

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $events[$row['id']] = $this->fromArray($row);
        }

        return $events;
    }

    /**
     * @param UuidInterface|null $previousEventId
     * @return Generator
     */
    public function iterate(UuidInterface $previousEventId = null)
    {
        $id = $previousEventId ? $this->getRowIdByEventId($previousEventId) : 0;

        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE id > ?'
            . ' ORDER BY id ASC'
            . ' LIMIT ?';

        $stmt = $this->connection->prepare($sql);

        while (true) {
            $stmt->bindValue(1, $id, Type::INTEGER);
            $stmt->bindValue(2, 100, Type::INTEGER);
            $stmt->execute();

            $count = 0;
            $lastId = false;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
                $lastId = $row['id'];
                yield $this->fromArray($row);
            }

            if ($count < 100 || !$lastId) {
                break;
            }

            $id = $lastId;
        }
    }

    /**
     * @param EventMessageInterface $event
     * @return array
     */
    private function toArray(EventMessageInterface $event)
    {
        $dateTimeFormat = $this->connection->getDatabasePlatform()->getDateTimeFormatString();
        $timestamp = $event->getTimestamp();

        $data = [
            'event_id' => (string) $event->getId(),
            'event_date' => $timestamp->format($dateTimeFormat),
            'event_date_u' => $timestamp->format('u'),
            'payload_type' => $event->getPayloadType(),
            'payload' => $this->serializer->serialize($event->getPayload()),
            'metadata' => $this->serializer->serialize($event->getMetadata()),
        ];

        if ($event instanceof DomainEventMessageInterface) {
            $aggregateId = $event->getAggregateId();
            if (!is_int($aggregateId)) {
                $aggregateId = (string) $aggregateId;
            }

            $data = array_merge($data, [
                'aggregate_type' => $event->getAggregateType(),
                'aggregate_id' => $aggregateId,
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
        $payload = $this->serializer->deserialize($data['payload'], $data['payload_type']);
        /** @var Metadata $metadata */
        $metadata = $this->serializer->deserialize($data['metadata'], Metadata::class);
        $id = Uuid::fromString($data['event_id']);
        $timestamp = DateTime::fromString("{$data['event_date']}.{$data['event_date_u']}");

        if (array_key_exists('aggregate_type', $data)) {
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
    private function getLastRowId()
    {
        $sql = 'SELECT MAX(id) FROM ' . $this->table;

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param UuidInterface $eventId
     * @return int
     */
    private function getRowIdByEventId(UuidInterface $eventId)
    {
        static $lastEventId, $lastRowId;

        if ($eventId->equals($lastEventId)) {
            return $lastRowId;
        }

        $sql = "SELECT id FROM {$this->table} WHERE event_id = ? LIMIT 1";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, (string) $eventId, Type::STRING);
        $stmt->execute();

        $rowId = $stmt->fetchColumn();
        if (false === $rowId) {
            throw new OutOfBoundsException(sprintf('Record for event %s not found', $eventId));
        }

        $lastEventId = $eventId;
        $lastRowId = (int) $rowId;
        return $lastRowId;
    }
}
