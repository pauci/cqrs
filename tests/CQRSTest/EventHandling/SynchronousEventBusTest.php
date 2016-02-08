<?php

namespace CQRSTest\EventHandling;

use CQRS\Domain\Message\GenericEventMessage;
use CQRS\EventHandling\EventExecutionFailed;
use CQRS\EventHandling\Locator\EventHandlerLocatorInterface;
use CQRS\EventHandling\SynchronousEventBus;
use Exception;
use PHPUnit_Framework_TestCase;
use Psr\Log\NullLogger;

class SynchronousEventBusTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SynchronousEventBus
     */
    private $eventBus;

    /**
     * @var SynchronousEventHandler
     */
    private $handler;

    /**
     * @var NullLogger
     */
    private $logger;

    public function setUp()
    {
        $this->handler = new SynchronousEventHandler();

        $locator = new SynchronousEventHandlerLocatorInterface();
        $locator->handler = $this->handler;

        $this->logger = new NullLogger();

        $this->eventBus = new SynchronousEventBus($locator, $this->logger);
    }

    public function testPublishingOfEvent()
    {
        $this->eventBus->publish(new GenericEventMessage(new SynchronousEvent()));

        $this->assertEquals(1, $this->handler->executed);
    }

    /**
     * @expectedException \CQRSTest\EventHandling\SomeException
     */
    public function testItRaisesEventExecutionFailedOnFailure()
    {
        $failureCausingEvent = new FailureCausingEvent();

        $this->eventBus->publish(new GenericEventMessage($failureCausingEvent));

        $failureEvent = $this->handler->failureEvent;

        $this->assertInstanceOf(EventExecutionFailed::class, $failureEvent);
        $this->assertInstanceOf(SomeException::class, $failureEvent->getException());
        $this->assertSame($failureCausingEvent, $failureEvent->getEvent()->getPayload());
    }

    public function testItIgnoresErrorWhenHandlingEventExecutionFailedEvent()
    {
        $failureEvent = new EventExecutionFailed(new GenericEventMessage(new FailureCausingEvent()), new SomeException());

        $this->handler->throwErrorOnEventExecutionFailed = true;

        $this->eventBus->publish(new GenericEventMessage($failureEvent));
    }
}

class SynchronousEventHandlerLocatorInterface implements EventHandlerLocatorInterface
{
    public $handler;

    public function getEventHandlers($eventName)
    {
        return [
            [$this->handler, 'on' . $eventName]
        ];
    }
}

class SynchronousEventHandler
{
    public $executed = 0;
    public $throwErrorOnEventExecutionFailed = false;
    /** @var EventExecutionFailed */
    public $failureEvent;

    public function onSynchronous(SynchronousEvent $event)
    {
        $this->executed++;
    }

    public function onFailureCausing(FailureCausingEvent $event)
    {
        throw new SomeException();
    }

    public function onEventExecutionFailed($event)
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
{}

class SomeException extends Exception
{}
