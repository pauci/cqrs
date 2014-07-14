<?php

namespace CQRS\EventHandling\Locator;

use CQRS\EventHandling\EventName;

interface EventHandlerLocatorInterface
{
    /**
     * @param EventName $eventName
     * @return array
     */
    public function getEventHandlers(EventName $eventName);
}
