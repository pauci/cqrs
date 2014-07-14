<?php

namespace CQRS\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\EventHandling\EventName;
use CQRS\EventStore\EventStoreInterface;
use CQRS\Serializer\Serializer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Zend\Json\Json;

class DbalEventStore implements EventStoreInterface
{
    /** @var Config */
    private $config;

    /** @var Connection */
    private $conn;

    /** @var Serializer */
    private $serializer;

    /**
     * @param Serializer $serializer
     * @param Connection $connection
     */
    public function __construct(Serializer $serializer, Connection $connection)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param DomainEventMessageInterface $event
     */
    public function store(DomainEventMessageInterface $event)
    {
        $sql = $this->getInsertEventSQL();

        $aggregateId = $event->getAggregateId();

        $params = [
            $event->getId()->getBytes(),
            $event->getAggregateType(),
            is_int($aggregateId) ? $aggregateId : (string) $aggregateId,
            (string) new EventName($event),
            $this->serialize($event),
            $event->getTimestamp()
        ];

        $types = [
            Type::BINARY,
            Type::STRING,
            is_int($aggregateId) ? Type::INTEGER : Type::STRING,
            Type::STRING,
            Type::STRING,
            TYPE::DATETIME
        ];

        $this->conn->executeUpdate($sql, $params, $types);
    }

    /**
     * @return string
     */
    private function getInsertEventSQL()
    {
        $table = $this->config->tableName;

        $columns = [
            $this->config->eventIdColumn,
            $this->config->aggregateTypeColumn,
            $this->config->aggregateIdColumn,
            $this->config->eventNameColumn,
            $this->config->eventDataColumn,
            $this->config->timestampColumn
        ];

        return "INSERT INTO {$table} (" . implode(', ', $columns) . ') VALUES (?, ?, ?, ?, ?, ?)';
    }

    /**
     * @param object $event
     * @return string
     */
    private function serialize($event)
    {
        $data = $this->serializer->toArray($event);
        return Json::encode($data);
    }
}
