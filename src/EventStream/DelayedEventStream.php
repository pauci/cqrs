<?php

declare(strict_types=1);

namespace CQRS\EventStream;

use CQRS\Domain\Message\EventMessageInterface;
use Generator;
use IteratorAggregate;
use Ramsey\Uuid\UuidInterface;

class DelayedEventStream implements IteratorAggregate, EventStreamInterface
{
    private EventStreamInterface $eventStream;

    private int $delaySeconds;

    public function __construct(EventStreamInterface $eventStream, int $delaySeconds)
    {
        $this->eventStream = $eventStream;
        $this->delaySeconds = $delaySeconds;
    }

    public function getLastEventId(): ?UuidInterface
    {
        return $this->eventStream->getLastEventId();
    }

    public function getIterator(): Generator
    {
        foreach ($this->eventStream as $offset => $event) {
            $this->delay($event);
            yield $offset => $event;
        }
    }

    private function delay(EventMessageInterface $event): void
    {
        $eventTime = $event->getTimestamp()->getTimestamp();
        $dispatchTime = $eventTime + $this->delaySeconds;

        $waitSeconds = $dispatchTime - time();
        while ($waitSeconds > 0) {
            sleep($waitSeconds);
            // Make sure it's time to dispatch, even if sleep has been interrupted by the process control signal
            $waitSeconds = $dispatchTime - time();
        }
    }
}
