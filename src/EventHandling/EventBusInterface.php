<?php

declare(strict_types=1);

namespace CQRS\EventHandling;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventStream\EventStreamInterface;
use Generator;

interface EventBusInterface
{
    public function publish(EventMessageInterface $event): void;

    public function publishFromStream(EventStreamInterface $eventStream): Generator;
}
