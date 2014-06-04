<?php

namespace CQRS\EventHandling;

interface EventHandlerLocator
{
    /**
     * @param EventName $eventName
     * @return array
     */
    public function getEventHandlers(EventName $eventName);
}
