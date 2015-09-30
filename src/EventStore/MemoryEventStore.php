<?php

namespace CQRS\EventStore;

use ArrayIterator;
use CQRS\Domain\Message\EventMessageInterface;
use Ramsey\Uuid\Uuid;

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
     * @param Uuid|null $previousEventId
     * @return ArrayIterator
     */
    public function iterate(Uuid $previousEventId = null)
    {
        return new ArrayIterator($this->events);
    }
}
