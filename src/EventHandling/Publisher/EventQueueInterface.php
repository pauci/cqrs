<?php

namespace CQRS\EventHandling\Publisher;

use CQRS\Domain\Message\EventMessageInterface;

interface EventQueueInterface
{
    /**
     * @return EventMessageInterface[]
     */
    public function dequeueAllEvents();
}
