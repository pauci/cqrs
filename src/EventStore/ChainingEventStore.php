<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Exception;
use Ramsey\Uuid\UuidInterface;
use Traversable;

class ChainingEventStore implements EventStoreInterface
{
    /**
     * @var EventStoreInterface[]
     */
    private $eventStores;

    /**
     * @param EventStoreInterface[] $eventStores
     */
    public function __construct(array $eventStores)
    {
        $this->eventStores = $eventStores;
    }

    /**
     * @param EventMessageInterface $event
     */
    public function store(EventMessageInterface $event)
    {
        foreach ($this->eventStores as $eventStore) {
            $eventStore->store($event);
        }
    }

    /**
     * @param int|null $offset
     * @param int $limit
     * @return array
     */
    public function read($offset = null, $limit = 10)
    {
        throw new Exception\BadMethodCallException(sprintf('%s does not support reading', self::class));
    }

    /**
     * @param null|UuidInterface $previousEventId
     * @return Traversable
     */
    public function iterate(UuidInterface $previousEventId = null)
    {
        throw new Exception\BadMethodCallException(sprintf('%s does not support iterating', self::class));
    }
}
