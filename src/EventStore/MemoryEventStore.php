<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;

class MemoryEventStore implements EventStoreInterface
{
    /**
     * @var EventMessageInterface[]
     */
    private array $events;

    public function store(EventMessageInterface $event): void
    {
        $this->events[] = $event;
    }

    public function pop(): ?EventMessageInterface
    {
        return array_shift($this->events);
    }
}
