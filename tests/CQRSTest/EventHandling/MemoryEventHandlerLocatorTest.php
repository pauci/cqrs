<?php

namespace CQRSTest\EventHandling;

use CQRS\EventHandling\EventName;
use CQRS\EventHandling\Locator\MemoryEventHandlerLocator;

class MemoryEventHandlerLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testItReturnsRegisteredCallbacksSortedByPriority()
    {
        $callback1 = function() {};
        $callback2 = function() {};
        $callback3 = function() {};
        $callback4 = function() {};

        $locator = new \CQRS\EventHandling\Locator\MemoryEventHandlerLocator();
        $locator->registerCallback('TestEvent', $callback1, 1);
        $locator->registerCallback('TestEvent', $callback2, -1);
        $locator->registerCallback('TestEvent', $callback3, 2);
        $locator->registerCallback('TestEvent', $callback4);
        $locator->registerCallback('AnotherEvent', function() {});

        $this->assertSame([
            $callback3,
            $callback1,
            $callback4,
            $callback2
        ], $locator->getEventHandlers(new LocatorTestEventName()));
    }

    public function testItReturnsCallbacksOnRegisteredSubscribersSortedByPriority()
    {
        $subscriber1 = new Subscriber();
        $subscriber2 = new Subscriber();

        $locator = new \CQRS\EventHandling\Locator\MemoryEventHandlerLocator();
        $locator->registerSubscriber($subscriber1);
        $locator->registerSubscriber($subscriber2, 10);
        $locator->registerSubscriber(new AnotherSubscriber());

        $handlers = $locator->getEventHandlers(new LocatorTestEventName());

        $this->assertSame([
            [$subscriber2, 'onTestEvent'],
            [$subscriber1, 'onTestEvent']
        ], $handlers);
    }

    public function testItReturnsEmptyArrayWhenNoHandlersAreRegistered()
    {
        $locator = new \CQRS\EventHandling\Locator\MemoryEventHandlerLocator();

        $this->assertEmpty($locator->getEventHandlers(new LocatorTestEventName()));
    }

    public function testItThrowsExceptionWhenRegisteredSubscriberIsNoObject()
    {
        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'No valid event handler given; expected object, got string'
        );

        $locator = new \CQRS\EventHandling\Locator\MemoryEventHandlerLocator();
        $locator->registerSubscriber('not an object');
    }
}

class LocatorTestEventName extends EventName
{
    public function __construct()
    {}

    public function __toString()
    {
        return 'TestEvent';
    }
}

class Subscriber
{
    public function onTestEvent()
    {}

    public function anotherMethod()
    {}
}

class AnotherSubscriber
{
    public function onAnotherEvent()
    {}
}
