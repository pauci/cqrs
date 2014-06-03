<?php

namespace CQRS\EventHandling;

interface EventHandlerLocator
{
    public function getEventHandlers($event);
}
