<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\EventMessageInterface;

interface EventBusInterface
{
    /**
     * @param EventMessageInterface $event
     * @return
     */
    public function publish(EventMessageInterface $event);
}
