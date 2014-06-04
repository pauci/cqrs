<?php

namespace CQRSTest\EventHandling;

use CQRS\EventHandling\EventName;
use CQRS\EventHandling\MemoryEventHandlerLocator;

class MemoryEventHandlerLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterEventHandler()
    {
        $handler = new MemoryEventHandlerLocatorTestEventHandler();

        $locator = new MemoryEventHandlerLocator();
        $locator->register($handler);

        $eventName = new MemoryEventHandlerLocatorTestEventName();

        $this->assertSame([$handler], $locator->getEventHandlers($eventName));
    }
}

class MemoryEventHandlerLocatorTestEventName extends EventName
{
    public function __construct()
    {}

    public function __toString()
    {
        return 'TestEvent';
    }
}

class MemoryEventHandlerLocatorTestEventHandler
{
    public function onTestEvent()
    {}
}
