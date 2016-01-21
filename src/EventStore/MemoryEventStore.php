<?php

namespace CQRS\EventStore;

use ArrayIterator;
use CQRS\Domain\Message\EventMessageInterface;
use Ramsey\Uuid\UuidInterface;

class MemoryEventStore implements EventStoreInterface
{
    /**
     * @var EventMessageInterface[]
     */
    private $events;

    /**
     * @param EventMessageInterface $event
     */
    public function store(EventMessageInterface $event)
    {
        $this->events[] = $event;
    }

    /**
     * @param int|null $offset
     * @param int $limit
     * @return array
     */
    public function read($offset = null, $limit = 10)
    {
        return array_slice($this->events, (int) $offset, $limit);
    }

    /**
     * @return ArrayIterator
     * @param UuidInterface|null $previousEventId
     */
    public function iterate(UuidInterface $previousEventId = null)
    {
        return new ArrayIterator($this->events);
    }
}
