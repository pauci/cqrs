<?php

declare(strict_types=1);

namespace CQRSTest\EventHandling;

use CQRS\Domain\Message\GenericEventMessage;
use CQRS\EventHandling\EventExecutionFailed;
use CQRS\EventHandling\SynchronousEventBus;
use CQRS\HandlerResolver\EventHandlerResolver;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class SynchronousEventBusTest extends TestCase
{
    private SynchronousEventBus $eventBus;

    /**
     * @var SynchronousEventHandler
     */
    private SynchronousEventHandler $handler;

    public function setUp(): void
    {
        $this->handler = new SynchronousEventHandler();

        $locator = new SynchronousEventHandlerLocator();
        $locator->handler = $this->handler;

        $this->eventBus = new SynchronousEventBus($locator);
    }

    public function testPublishingOfEvent(): void
    {
        $this->eventBus->publish(new GenericEventMessage(new SynchronousEvent()));

        self::assertEquals(1, $this->handler->executed);
    }

    public function testItRaisesEventExecutionFailedOnFailure(): void
    {
        $this->expectException(SomeException::class);

        $failureCausingEvent = new FailureCausingEvent();

        $this->eventBus->publish(new GenericEventMessage($failureCausingEvent));

        $failureEvent = $this->handler->failureEvent;

        self::assertInstanceOf(EventExecutionFailed::class, $failureEvent);
        self::assertInstanceOf(SomeException::class, $failureEvent->getException());
        self::assertSame($failureCausingEvent, $failureEvent->getEvent()->getPayload());
    }

    public function testItIgnoresErrorWhenHandlingEventExecutionFailedEvent(): void
    {
        $failureEvent = new EventExecutionFailed(
            new GenericEventMessage(
                new FailureCausingEvent()
            ),
            new SomeException()
        );

        $this->handler->throwErrorOnEventExecutionFailed = true;

        $this->eventBus->publish(new GenericEventMessage($failureEvent));

        self::assertTrue(true);
    }
}

class SynchronousEventHandlerLocator implements ContainerInterface
{
    public $handler;

    public function get($eventType)
    {
        $resolver = new EventHandlerResolver();
        $handler = $resolver($this->handler, $eventType);

        return [
            $handler
        ];
    }

    public function has($eventType): bool
    {
        return true;
    }
}

class SynchronousEventHandler
{
    public int $executed = 0;

    public bool $throwErrorOnEventExecutionFailed = false;

    public EventExecutionFailed $failureEvent;

    public function onSynchronous(SynchronousEvent $event): void
    {
        $this->executed++;
    }

    public function onFailureCausing(FailureCausingEvent $event): void
    {
        throw new SomeException();
    }

    public function onEventExecutionFailed($event): void
    {
        if ($this->throwErrorOnEventExecutionFailed) {
            throw new SomeException();
        }

        $this->failureEvent = $event;
    }
}

class SynchronousEvent
{
}

class FailureCausingEvent
{
}

class SomeException extends Exception
{
}
