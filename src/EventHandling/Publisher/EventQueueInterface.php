<?php

declare(strict_types=1);

namespace CQRS\EventHandling\Publisher;

use CQRS\Domain\Message\EventMessageInterface;

interface EventQueueInterface
{
    /**
     * @return EventMessageInterface[]
     */
    public function dequeueAllEvents(): array;
}
