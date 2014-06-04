<?php

namespace CQRS\EventHandling;

interface EventHandlerLocator
{
    /**
     * @param DomainEvent $event
     * @return array
     */
    public function getEventHandlers(DomainEvent $event);
}
