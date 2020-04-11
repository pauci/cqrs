<?php

declare(strict_types=1);

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
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Types\Types;
use Generator;
use Pauci\DateTime\DateTime;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DbalEventStore implements EventStoreInterface
{
    private Config $config;

    private Connection $connection;

    private SerializerInterface $serializer;

    private ?string $dateTimeFormat = null;

    public function __construct(Config $config, Connection $connection, SerializerInterface $serializer)
    {
        $this->config = $config;
        $this->connection = $connection;
        $this->serializer = $serializer;
    }

    public function store(EventMessageInterface $event): void
    {
        $data = [
            'event_id' => $event->getId(),
            'event_date' => $event->getTimestamp()->format($this->getDateTimeFormat()),
            'aggregate_type' => null,
            'aggregate_id' => null,
            'sequence_number' => null,
            'payload_type' => $event->getPayloadType(),
            'payload' => $this->serializer->serialize($event->getPayload()),
            'metadata' => $this->serializer->serialize($event->getMetadata()),
        ];

        if ($event instanceof DomainEventMessageInterface) {
            $data['aggregate_type'] = $event->getAggregateType();
            $data['aggregate_id'] = $event->getAggregateId();
            $data['sequence_number'] = $event->getSequenceNumber();
        }

        $types = [
            'event_id' => UuidType::NAME,
            'sequence_number' => Types::INTEGER,
        ];

        $this->connection->insert($this->config->getTableName(), $data, $types);
    }

    /**.
     * @return EventMessageInterface[]
     * @throws DBALException
     */
    public function read(int $offset = 0, int $limit = 10): array
    {
        if ($offset === -1) {
            $offset = (((int) (($this->getLastRowId() - 1) / $limit)) * $limit) + 1;
        }

        $sql = 'SELECT * FROM ' . $this->config->getTableName()
            . ' WHERE id >= ?'
            . ' ORDER BY id ASC'
            . ' LIMIT ?';

        $events = [];

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $offset, Types::INTEGER);
        $stmt->bindValue(2, $limit, Types::INTEGER);
        $stmt->execute();

        while ($row = $stmt->fetch(FetchMode::ASSOCIATIVE)) {
            $events[$row['id']] = $this->fromArray($row);
        }

        return $events;
    }

    /**
     * @return Generator<EventMessageInterface>
     * @throws DBALException
     */
    public function iterate(UuidInterface $previousEventId = null): Generator
    {
        $id = $previousEventId ? $this->getRowIdByEventId($previousEventId) : 0;

        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE id > ?'
            . ' ORDER BY id ASC'
            . ' LIMIT ?';

        $stmt = $this->connection->prepare($sql);

        while (true) {
            $stmt->bindValue(1, $id, Types::INTEGER);
            $stmt->bindValue(2, 100, Types::INTEGER);
            $stmt->execute();

            $count = 0;
            $lastId = false;
            while ($row = $stmt->fetch(FetchMode::ASSOCIATIVE)) {
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

    private function toArray(EventMessageInterface $event): array
    {
        $data = [
            'event_id' => $event->getId()->toString(),
            'event_date' => $event->getTimestamp()->format($this->getDateTimeFormat()),
            'aggregate_type' => null,
            'aggregate_id' => null,
            'sequence_number' => null,
            'payload_type' => $event->getPayloadType(),
            'payload' => $this->serializer->serialize($event->getPayload()),
            'metadata' => $this->serializer->serialize($event->getMetadata()),
        ];

        if ($event instanceof DomainEventMessageInterface) {
            $data['aggregate_type'] = $event->getAggregateType();
            $data['aggregate_id'] = (string) $event->getAggregateId();
            $data['sequence_number'] = $event->getSequenceNumber();
        }

        return $data;
    }

    private function getDateTimeFormat(): string
    {
        if (null === $this->dateTimeFormat) {
            $this->dateTimeFormat = $this->connection->getDatabasePlatform()
                ->getDateTimeFormatString();

            if (false === strpos($this->dateTimeFormat, 's.')) {
                $this->dateTimeFormat = str_replace('s', 's.u', $this->dateTimeFormat);
            }
        }

        return $this->dateTimeFormat;
    }

    /**
     * @return GenericDomainEventMessage|GenericEventMessage
     */
    public function fromArray(array $data): GenericEventMessage
    {
        $payload = $this->serializer->deserialize($data['payload'], $data['payload_type']);
        /** @var Metadata $metadata */
        $metadata = $this->serializer->deserialize($data['metadata'], Metadata::class);
        $id = Uuid::fromString($data['event_id']);
        $timestamp = DateTime::fromString($data['event_date']);

        if (isset($data['aggregate_type'], $data['aggregate_id'], $data['sequence_number'])) {
            return new GenericDomainEventMessage(
                $data['aggregate_type'],
                $data['aggregate_id'],
                (int) $data['sequence_number'],
                $payload,
                $metadata,
                $id,
                $timestamp
            );
        }

        return new GenericEventMessage($payload, $metadata, $id, $timestamp);
    }

    private function getLastRowId(): int
    {
        $sql = 'SELECT MAX(id) FROM ' . $this->config->getTableName();

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    private function getRowIdByEventId(UuidInterface $eventId): int
    {
        static $lastEventId, $lastRowId;

        if ($eventId->equals($lastEventId)) {
            return $lastRowId;
        }

        $sql = 'SELECT id FROM ' . $this->config->getTableName() . ' WHERE event_id = ? LIMIT 1';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, (string) $eventId, Types::STRING);
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
