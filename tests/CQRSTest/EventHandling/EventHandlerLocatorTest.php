<?php

namespace CQRSTest\EventHandling;

use CQRS\EventHandling\EventHandlerLocator;
use PHPUnit_Framework_TestCase;

class EventHandlerLocatorTest extends PHPUnit_Framework_TestCase
{
    public function testItReturnsRegisteredCallbacksSortedByPriority()
    {
        $locator = new EventHandlerLocator();
        $locator->add('TestEvent', 'handler1', 10);
        $locator->add('TestEvent', 'handler2', -1);
        $locator->add('TestEvent', 'handler3', 20);
        $locator->add('TestEvent', 'handler4');
        $locator->add('AnotherEvent', function() {});

        $this->assertSame([
            'handler3',
            'handler1',
            'handler4',
            'handler2'
        ], $locator->get('TestEvent'));
    }

    public function testItReturnsEmptyArrayWhenNoHandlersAreRegistered()
    {
        $locator = new EventHandlerLocator();

        $this->assertEmpty($locator->get('TestEvent'));
    }
}
