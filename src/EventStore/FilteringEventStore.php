<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use Composer\EventDispatcher\Event;
use CQRS\Domain\Message\EventMessageInterface;
use Ramsey\Uuid\UuidInterface;
use Traversable;

class FilteringEventStore implements EventStoreInterface
{
    private EventStoreInterface $eventStore;

    private EventFilterInterface $filter;

    public function __construct(EventStoreInterface $eventStore, EventFilterInterface $filter)
    {
        $this->eventStore = $eventStore;
        $this->filter     = $filter;
    }

    public function store(EventMessageInterface $event): void
    {
        if ($this->filter->isValid($event)) {
            $this->eventStore->store($event);
        }
    }

    /**
     * @return EventMessageInterface[]
     */
    public function read(int $offset = 0, int $limit = 10): array
    {
        return $this->eventStore->read($offset, $limit);
    }

    /**
     * @return Traversable<EventMessageInterface>
     */
    public function iterate(UuidInterface $previousEventId = null): Traversable
    {
        return $this->eventStore->iterate($previousEventId);
    }
}
