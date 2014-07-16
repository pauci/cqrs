<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\DomainEventInterface;
use CQRS\Domain\Message\EventInterface;
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
    public function __construct(EventHandlerLocatorInterface $locator, EventStoreInterface $eventStore = null)
    {
        $this->locator    = $locator;
        $this->eventStore = $eventStore;
    }

    /**
     * @param EventInterface $event
     */
    public function publish(EventInterface $event)
    {
        if ($this->eventStore && $event instanceof DomainEventInterface) {
            $this->eventStore->store($event);
        }

        $eventName = $event->getEventName();
        $callbacks = $this->locator->getEventHandlers($eventName);

        foreach ($callbacks as $callback) {
            $this->invokeEventHandler($callback, $event);
        }
    }

    /**
     * @param Callable $callback
     * @param EventInterface $event
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
