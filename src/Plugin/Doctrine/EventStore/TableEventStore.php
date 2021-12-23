<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventStore\EventStoreInterface;
use CQRS\Serializer\SerializerInterface;
use Doctrine\DBAL\Connection;
use Stringable;

class TableEventStore implements EventStoreInterface
{
    private SerializerInterface $serializer;

    private Connection $connection;

    private string $table = 'cqrs_event';

    public function __construct(SerializerInterface $serializer, Connection $connection, string $table = null)
    {
        $this->serializer = $serializer;
        $this->connection = $connection;

        if (null !== $table) {
            $this->table = $table;
        }
    }

    public function store(EventMessageInterface $event): void
    {
        $data = $this->toArray($event);
        $this->connection->insert($this->table, $data);
    }

    private function toArray(EventMessageInterface $event): array
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
            if ($aggregateId instanceof Stringable) {
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
}
