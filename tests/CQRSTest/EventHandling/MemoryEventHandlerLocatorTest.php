<?php

namespace CQRSTest\EventHandling;

use CQRS\EventHandling\Locator\MemoryEventHandlerLocator;
use CQRS\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;

class MemoryEventHandlerLocatorTest extends PHPUnit_Framework_TestCase
{
    public function testItReturnsRegisteredCallbacksSortedByPriority()
    {
        $callback1 = function() {};
        $callback2 = function() {};
        $callback3 = function() {};
        $callback4 = function() {};

        $locator = new MemoryEventHandlerLocator();
        $locator->addListener('TestEvent', $callback1, 1);
        $locator->addListener('TestEvent', $callback2, -1);
        $locator->addListener('TestEvent', $callback3, 2);
        $locator->addListener('TestEvent', $callback4);
        $locator->addListener('AnotherEvent', function() {});

        $this->assertSame([
            $callback3,
            $callback1,
            $callback4,
            $callback2
        ], $locator->getEventHandlers('TestEvent'));
    }

    public function testItReturnsCallbacksOnRegisteredSubscribersSortedByPriority()
    {
        $subscriber1 = new Subscriber();
        $subscriber2 = new Subscriber();

        $locator = new MemoryEventHandlerLocator();
        $locator->addSubscriber($subscriber1);
        $locator->addSubscriber($subscriber2, 10);
        $locator->addSubscriber(new AnotherSubscriber());

        $handlers = $locator->getEventHandlers('TestEvent');

        $this->assertSame([
            [$subscriber2, 'onTestEvent'],
            [$subscriber1, 'onTestEvent']
        ], $handlers);
    }

    public function testItReturnsEmptyArrayWhenNoHandlersAreRegistered()
    {
        $locator = new MemoryEventHandlerLocator();

        $this->assertEmpty($locator->getEventHandlers('TestEvent'));
    }

    public function testItThrowsExceptionWhenRegisteredSubscriberIsNoObject()
    {
        $this->setExpectedException(
            RuntimeException::class,
            'No valid event subscriber given; expected object, got string'
        );

        $locator = new MemoryEventHandlerLocator();
        $locator->addSubscriber('not an object');
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
