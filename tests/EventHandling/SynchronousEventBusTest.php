<?php

declare(strict_types=1);

namespace CQRSTest\EventHandling;

use CQRS\Domain\Message\GenericEventMessage;
use CQRS\EventHandling\EventExecutionFailed;
use CQRS\EventHandling\EventHandlerLocatorInterface;
use CQRS\EventHandling\SynchronousEventBus;
use CQRSTest\Stubs\DummyCallableContainer;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class SynchronousEventBusTest extends TestCase
{
    private SynchronousEventBus $eventBus;

    private Stubs\DummyEventHandler $handler;

    public function setUp(): void
    {
        $this->handler = new Stubs\DummyEventHandler();

        $locator = new Stubs\DummyEventHandlerLocator();
        $locator->handler = $this->handler;

        $this->eventBus = new SynchronousEventBus($locator);
    }

    public function testPublishingOfEvent(): void
    {
        $this->eventBus->publish(new GenericEventMessage(new Stubs\SynchronousEvent()));

        self::assertEquals(1, $this->handler->executed);
    }

    public function testItRaisesEventExecutionFailedOnFailure(): void
    {
        $this->expectException(Stubs\SomeException::class);

        $failureCausingEvent = new Stubs\FailureCausingEvent();

        $this->eventBus->publish(new GenericEventMessage($failureCausingEvent));

        $failureEvent = $this->handler->failureEvent;

        self::assertInstanceOf(EventExecutionFailed::class, $failureEvent);
        self::assertInstanceOf(Stubs\SomeException::class, $failureEvent->getException());
        self::assertSame($failureCausingEvent, $failureEvent->getEvent()->getPayload());
    }

    public function testItIgnoresErrorWhenHandlingEventExecutionFailedEvent(): void
    {
        $failureEvent = new EventExecutionFailed(
            new GenericEventMessage(
                new Stubs\FailureCausingEvent()
            ),
            new Stubs\SomeException()
        );

        $this->handler->throwErrorOnEventExecutionFailed = true;

        $this->eventBus->publish(new GenericEventMessage($failureEvent));

        self::assertTrue(true);
    }
}
