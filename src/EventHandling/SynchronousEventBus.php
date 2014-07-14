<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventHandling\Locator\EventHandlerLocatorInterface;
use CQRS\EventStore\EventStoreInterface;
use Exception;

class SynchronousEventBus implements EventBusInterface
{
    /** @var EventHandlerLocatorInterface */
    private $locator;

    /** @var EventStoreInterface */
    private $eventStore;

    /**
     * @param EventHandlerLocatorInterface $locator
     * @param EventStoreInterface $eventStore
     */
    public function __construct(EventHandlerLocatorInterface $locator, EventStoreInterface $eventStore)
    {
        $this->locator    = $locator;
        $this->eventStore = $eventStore;
    }

    /**
     * @param EventMessageInterface $event
     */
    public function publish(EventMessageInterface $event)
    {
        if ($event instanceof DomainEventMessageInterface) {
            $this->eventStore->store($event);
        }

        $eventName = new EventName($event);
        $callbacks = $this->locator->getEventHandlers($eventName);

        foreach ($callbacks as $callback) {
            $this->invokeEventHandler($callback, $event);
        }
    }

    /**
     * @param Callable $callback
     * @param \CQRS\Domain\Message\EventMessageInterface $event
     */
    private function invokeEventHandler(callable $callback, $event)
    {
        try {
            $callback($event);
        } catch (Exception $e) {
            if ($event instanceof EventExecutionFailed) {
                return;
            }

            $this->publish(new EventExecutionFailed([
                'exception' => $e,
                'event'     => $event,
            ]));
        }
    }
}
