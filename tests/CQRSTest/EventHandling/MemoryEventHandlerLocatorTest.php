<?php

namespace CQRSTest\EventHandling;

use CQRS\EventHandling\EventName;
use CQRS\EventHandling\MemoryEventHandlerLocator;

class MemoryEventHandlerLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterAndGetEventHandlers()
    {
        $handler = new EventHandlerToRegister();

        $locator = new MemoryEventHandlerLocator();
        $locator->register($handler);

        $eventName = new EventNameToRegister();

        $this->assertSame([$handler], $locator->getEventHandlers($eventName));
    }

    public function testGetEmptyEventHandlers()
    {
        $locator = new MemoryEventHandlerLocator();

        $this->assertEmpty($locator->getEventHandlers(new EventNameToRegister()));
    }

    public function testItThrowsExceptionWhenRegisteredHandlerIsNoObject()
    {
        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'No valid event handler given; expected object, got string'
        );

        $locator = new MemoryEventHandlerLocator();
        $locator->register('not an object');
    }
}

class EventNameToRegister extends EventName
{
    public function __construct()
    {}

    public function __toString()
    {
        return 'TestEvent';
    }
}

class EventHandlerToRegister
{
    public function onTestEvent()
    {}

    public function anotherMethod()
    {}
}
