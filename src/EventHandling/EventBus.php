<?php

namespace CQRS\EventHandling;

interface EventBus
{
    /**
     * @param DomainEvent $event
     */
    public function publish(DomainEvent $event);
}
