<?php

declare(strict_types=1);

namespace CQRS\EventHandling;

use CQRS\EventStream\EventStreamInterface;
use Generator;

abstract class AbstractEventBus implements EventBusInterface
{
    public function publishFromStream(EventStreamInterface $eventStream): Generator
    {
        foreach ($eventStream as $event) {
            $this->publish($event);
            yield $event;
        }
    }
}
