<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use Generator;
use Ramsey\Uuid\UuidInterface;

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

    /**
     * @return EventMessageInterface[]
     */
    public function read(int $offset = 0, int $limit = 10): array
    {
        return array_slice($this->events, $offset, $limit);
    }

    /**
     * @return Generator<EventMessageInterface>
     */
    public function iterate(UuidInterface $previousEventId = null): Generator
    {
        $yield = !$previousEventId;
        foreach ($this->events as $event) {
            if ($yield) {
                yield $event;
            } elseif ($event->getId()->equals($previousEventId)) {
                $yield = true;
            }
        }
    }
}
