<?php

namespace CQRS\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\DomainEventInterface;
use CQRS\EventStore\EventStoreInterface;
use CQRS\Serializer\SerializerInterface;
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
    public function __construct(SerializerInterface $serializer, Connection $connection, $table = 'cqrs_domain_event')
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
     * @param int|null $offset
     * @param int $limit
     * @return array
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
            $events[$row['id']] = $row['data'];
            //$events[$row['id']] = $this->serializer->deserialize('', $row['data'], 'json');
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
