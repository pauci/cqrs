<?php

namespace CQRSTest\EventHandling;

use CQRS\EventHandling\DomainEvent;
use CQRS\EventHandling\EventHandlerLocator;
use CQRS\EventHandling\EventName;
use CQRS\EventHandling\SynchronousEventBus;

class SynchronousEventBusTest extends \PHPUnit_Framework_TestCase
{
    public function testPublishEvent()
    {
        $locator = new SynchronousEventBusTestEventHandlerLocator();
        $locator->handler = new SynchronousEventBusTestEventHandler();

        $eventBus = new SynchronousEventBus($locator);
        $eventBus->publish(new SynchronousEventBusTestedEvent());

        $this->assertEquals(1, $locator->handler->executed);
    }
}

class SynchronousEventBusTestEventHandlerLocator implements EventHandlerLocator
{
    public $handler;

    public function getEventHandlers(EventName $eventName)
    {
        return [$this->handler];
    }
}

class SynchronousEventBusTestEventHandler
{
    public $executed = 0;

    public function onSynchronousEventBusTested(SynchronousEventBusTestedEvent $event)
    {
        $this->executed++;
    }
}

class SynchronousEventBusTestedEvent implements DomainEvent
{}
