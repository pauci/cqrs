<?php

namespace CQRS\EventHandling;

use CQRS\EventStream\EventStreamInterface;
use Generator;

abstract class AbstractEventBus implements EventBusInterface
{
    /**
     * @param EventStreamInterface $eventStream
     * @return Generator
     */
    public function publishFromStream(EventStreamInterface $eventStream)
    {
        foreach ($eventStream as $event) {
            $this->publish($event);
            yield $event;
        }
    }
}
