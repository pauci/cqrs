<?php

declare(strict_types=1);

namespace CQRS\EventStream;

use Generator;
use IteratorAggregate;
use Ramsey\Uuid\UuidInterface;

class ContinuousEventStream implements IteratorAggregate, EventStreamInterface
{
    private EventStreamInterface $eventStream;

    private int $pauseMicroseconds;

    public function __construct(EventStreamInterface $eventStream, int $pauseMicroseconds = 500000)
    {
        $this->eventStream = $eventStream;
        $this->pauseMicroseconds = $pauseMicroseconds;
    }

    public function getLastEventId(): ?UuidInterface
    {
        return $this->eventStream->getLastEventId();
    }

    public function getIterator(): Generator
    {
        while (true) {
            foreach ($this->eventStream as $event) {
                yield $event;
            }
            usleep($this->pauseMicroseconds);
        }
    }
}
