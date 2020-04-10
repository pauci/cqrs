<?php

declare(strict_types=1);

namespace CQRS\EventStream;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventStore\EventStoreInterface;
use Generator;
use IteratorAggregate;
use Ramsey\Uuid\UuidInterface;

class EventStoreEventStream implements IteratorAggregate, EventStreamInterface
{
    private EventStoreInterface $eventStore;

    private ?UuidInterface $lastEventId;

    public function __construct(EventStoreInterface $eventStore, UuidInterface $previousEventId = null)
    {
        $this->eventStore = $eventStore;
        $this->lastEventId = $previousEventId;
    }

    public function getLastEventId(): ?UuidInterface
    {
        return $this->lastEventId;
    }

    public function getIterator(): Generator
    {
        $eventIterator = $this->eventStore->iterate($this->lastEventId);

        /** @var EventMessageInterface $event */
        foreach ($eventIterator as $event) {
            $this->lastEventId = $event->getId();
            yield $event;
        }
    }
}
