<?php

declare(strict_types=1);

namespace CQRSTest\EventHandling\Stubs;

use CQRS\EventHandling\EventExecutionFailed;
use CQRS\EventHandling\EventHandlerLocatorInterface;

class DummyEventHandlerLocator implements EventHandlerLocatorInterface
{
    public DummyEventHandler $handler;

    public function get(string $eventType): array
    {
        return [
            match ($eventType) {
                SynchronousEvent::class => [$this->handler, 'onSynchronous'],
                FailureCausingEvent::class => [$this->handler, 'onFailureCausing'],
                EventExecutionFailed::class => [$this->handler, 'onEventExecutionFailed'],
            }
        ];
    }
}
