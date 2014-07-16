<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\EventInterface;

interface EventBusInterface
{
    /**
     * @param EventInterface $event
     * @return
     */
    public function publish(EventInterface $event);
}
