<?php
declare(strict_types=1);

namespace CQRS\EventHandling;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventStream\EventStreamInterface;
use Generator;

interface EventBusInterface
{
    /**
     * @param EventMessageInterface $event
     */
    public function publish(EventMessageInterface $event): void;

    /**
     * @param EventStreamInterface $eventStream
     * @return Generator
     */
    public function publishFromStream(EventStreamInterface $eventStream);
}
