<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;

class FilteringEventStore implements EventStoreInterface
{
    private EventStoreInterface $eventStore;

    private EventFilterInterface $filter;

    public function __construct(EventStoreInterface $eventStore, EventFilterInterface $filter)
    {
        $this->eventStore = $eventStore;
        $this->filter = $filter;
    }

    public function store(EventMessageInterface $event): void
    {
        if ($this->filter->isValid($event)) {
            $this->eventStore->store($event);
        }
    }
}
