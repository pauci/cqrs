<?php

namespace CQRSTest\EventHandling;

use CQRS\EventHandling\DomainEvent;
use CQRS\EventHandling\EventExecutionFailed;
use CQRS\EventHandling\EventHandlerLocator;
use CQRS\EventHandling\EventName;
use CQRS\EventHandling\SynchronousEventBus;

class SynchronousEventBusTest extends \PHPUnit_Framework_TestCase
{
    /** @var SynchronousEventBus */
    protected $eventBus;
    /** @var SynchronousEventHandler */
    protected $handler;

    public function setUp()
    {
        $this->handler = new SynchronousEventHandler();

        $locator = new SynchronousEventHandlerLocator();
        $locator->handler = $this->handler;

        $this->eventBus = new SynchronousEventBus($locator);
    }

    public function testPublishingOfEvent()
    {
        $this->eventBus->publish(new SynchronousEvent());

        $this->assertEquals(1, $this->handler->executed);
    }

    public function testItRaisesEventExecutionFailedOnFailure()
    {
        $failureCausingEvent = new FailureCausingEvent();

        $this->eventBus->publish($failureCausingEvent);

        $failureEvent = $this->handler->failureEvent;

        $this->assertInstanceOf('CQRS\EventHandling\EventExecutionFailed', $failureEvent);
        $this->assertInstanceOf('CQRSTest\EventHandling\EventHandlingException', $failureEvent->exception);
        $this->assertSame($failureCausingEvent, $failureEvent->event);
    }

    public function testItIgnoresErrorWhenHandlingEventExecutionFailedEvent()
    {
        $failureEvent = new EventExecutionFailed();

        $this->handler->throwErrorOnEventExecutionFailed = true;

        $this->eventBus->publish($failureEvent);
    }
}

class SynchronousEventHandlerLocator implements EventHandlerLocator
{
    public $handler;

    public function getEventHandlers(EventName $eventName)
    {
        return [[$this->handler, 'on' . $eventName]];
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
        throw new EventHandlingException();
    }

    public function onEventExecutionFailed($event)
    {
        if ($this->throwErrorOnEventExecutionFailed) {
            throw new EventHandlingException();
        }

        $this->failureEvent = $event;
    }
}

class SynchronousEvent implements DomainEvent
{}

class FailureCausingEvent implements DomainEvent
{}

class EventHandlingException extends \Exception
{}
