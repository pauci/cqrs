<?php

namespace CQRS\EventHandling\Locator;

interface EventHandlerLocatorInterface
{
    /**
     * @param string $eventName
     * @return array
     */
    public function getEventHandlers($eventName);
}
