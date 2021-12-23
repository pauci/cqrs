<?php

declare(strict_types=1);

namespace CQRSTest\EventHandling\Stubs;

use CQRS\EventHandling\EventExecutionFailed;
use CQRSTest\EventHandling\Stubs\FailureCausingEvent;
use CQRSTest\EventHandling\Stubs\SynchronousEvent;

class DummyEventHandler
{
    public int $executed = 0;

    public bool $throwErrorOnEventExecutionFailed = false;

    public ?EventExecutionFailed $failureEvent = null;

    public function onSynchronous(SynchronousEvent $event): void
    {
        $this->executed++;
    }

    public function onFailureCausing(FailureCausingEvent $event): void
    {
        throw new SomeException();
    }

    public function onEventExecutionFailed($event): void
    {
        if ($this->throwErrorOnEventExecutionFailed) {
            throw new SomeException();
        }

        $this->failureEvent = $event;
    }
}
