<?php

namespace CQRS\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\DomainEventInterface;
use CQRS\EventStore\EventStoreInterface;
use CQRS\Serializer\SerializerInterface;
use Doctrine\DBAL\Connection;
use Rhumsaa\Uuid\Uuid;

class TableEventStore implements EventStoreInterface
{
    /** @var Connection */
    private $connection;

    /** @var string */
    private $table;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param Connection $connection
     * @param SerializerInterface $serializer
     * @param string $table
     */
    public function __construct(Connection $connection, SerializerInterface $serializer, $table = 'cqrs_domain_event')
    {
        $this->connection = $connection;
        $this->serializer = $serializer;
        $this->table      = $table;
    }

    /**
     * @param DomainEventInterface $event
     */
    public function store(DomainEventInterface $event)
    {
        $aggregateId = $event->getAggregateId();
        if (is_array($aggregateId)) {
            $aggregateId = json_encode($aggregateId);
        } elseif ($aggregateId instanceof Uuid) {
            $aggregateId = (string) $aggregateId;
        }

        $this->connection->insert($this->table, [
            'event_id'       => (string) $event->getId(),
            'event_date'     => $event->getTimestamp()->format('Y-m-d H:i:s'), // looses microseconds precision
            'aggregate_type' => $event->getAggregateType(),
            'aggregate_id'   => $aggregateId,
            'event_name'     => $event->getEventName(),
            'data'           => $this->serializer->serialize($event, 'json')
        ]);
    }

    /**.
     * @param int $pageSize
     * @param int|null $firstId
     * @return array
     */
    public function readPage($pageSize = 10, $firstId = null)
    {
        if ($firstId === null) {
            $firstId = (((int) (($this->getLastId() - 1) / $pageSize)) * $pageSize) + 1;
        }

        $sql = 'SELECT * FROM ' . $this->table
            . ' WHERE id >= ?'
            . ' ORDER BY id ASC'
            . ' LIMIT ?';

        $events = [];

        $stmt = $this->connection->executeQuery($sql, [$firstId, $pageSize]);

        foreach ($stmt as $row) {
            $events[$row['id']] = $this->serializer->deserialize('', $row['data'], 'json');
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
