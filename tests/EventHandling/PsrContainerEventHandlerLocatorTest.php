<?php

declare(strict_types=1);

namespace CQRSTest\EventHandling;

use CQRS\EventHandling\PsrContainerEventHandlerLocator;
use CQRSTest\Stubs\DummyCallableContainer;
use PHPUnit\Framework\TestCase;

class PsrContainerEventHandlerLocatorTest extends TestCase
{
    public function testItReturnsRegisteredCallbacksSortedByPriority(): void
    {
        $locator = new PsrContainerEventHandlerLocator(new DummyCallableContainer());
        $locator->add('TestEvent', 'handler1', 10);
        $locator->add('TestEvent', 'handler2', -1);
        $locator->add('TestEvent', 'handler3', 20);
        $locator->add('TestEvent', 'handler4');
        $locator->add('AnotherEvent', 'anotherHandler');

        $handlers = $locator->get('TestEvent');
        $ids = array_map(fn ($handler) => $handler(), $handlers);

        self::assertSame([
            'handler3',
            'handler1',
            'handler4',
            'handler2'
        ], $ids);
    }

    public function testItReturnsEmptyArrayWhenNoHandlersAreRegistered(): void
    {
        $locator = new PsrContainerEventHandlerLocator(new DummyCallableContainer());

        self::assertEmpty($locator->get('TestEvent'));
    }
}
