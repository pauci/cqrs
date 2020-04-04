<?php

declare(strict_types=1);

namespace CQRSTest\EventHandling;

use CQRS\EventHandling\EventHandlerLocator;
use PHPUnit\Framework\TestCase;

class EventHandlerLocatorTest extends TestCase
{
    public function testItReturnsRegisteredCallbacksSortedByPriority(): void
    {
        $locator = new EventHandlerLocator();
        $locator->add('TestEvent', 'handler1', 10);
        $locator->add('TestEvent', 'handler2', -1);
        $locator->add('TestEvent', 'handler3', 20);
        $locator->add('TestEvent', 'handler4');
        $locator->add('AnotherEvent', static function() {});

        $this->assertSame([
            'handler3',
            'handler1',
            'handler4',
            'handler2'
        ], $locator->get('TestEvent'));
    }

    public function testItReturnsEmptyArrayWhenNoHandlersAreRegistered(): void
    {
        $locator = new EventHandlerLocator();

        $this->assertEmpty($locator->get('TestEvent'));
    }
}
