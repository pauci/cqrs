<?php

namespace CQRS\EventStream;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventStore\EventStoreInterface;
use Generator;
use IteratorAggregate;
use Ramsey\Uuid\Uuid;

class EventStoreEventStream implements IteratorAggregate, EventStreamInterface
{
    /**
     * @var EventStoreInterface
     */
    private $eventStore;

    /**
     * @var Uuid
     */
    private $previousEventId;

    /**
     * @param EventStoreInterface $eventStore
     * @param Uuid|null           $previousEventId
     */
    public function __construct(EventStoreInterface $eventStore, Uuid $previousEventId = null)
    {
        $this->eventStore = $eventStore;
        $this->previousEventId = $previousEventId;
    }

    /**
     * @return Generator
     */
    public function getIterator()
    {
        $eventIterator = $this->eventStore->iterate($this->previousEventId);

        /** @var EventMessageInterface $event */
        foreach ($eventIterator as $event) {
            $this->previousEventId = $event->getId();
            yield $event;
        }
}
}
