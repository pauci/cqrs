<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericEventMessage;
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
     * @return EventHandlerLocatorInterface
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * @return EventStoreInterface
     */
    public function getEventStore()
    {
        return $this->eventStore;
    }

    /**
     * @param EventMessageInterface $event
     */
    public function publish(EventMessageInterface $event)
    {
        if ($this->eventStore && $event instanceof DomainEventMessageInterface) {
            $this->eventStore->store($event);
        }

        $eventName = $this->getEventName($event);
        $callbacks = $this->locator->getEventHandlers($eventName);

        foreach ($callbacks as $callback) {
            $this->invokeEventHandler($callback, $event);
        }
    }

    /**
     * @param Callable $callback
     * @param EventMessageInterface $event
     */
    private function invokeEventHandler(callable $callback, $event)
    {
        try {
            if ($event instanceof DomainEventMessageInterface) {
                $callback(
                    $event->getPayload(),
                    $event->getMetadata(),
                    $event->getTimestamp(),
                    $event->getSequenceNumber(),
                    $event->getAggregateId()
                );
            } else {
                $callback(
                    $event->getPayload(),
                    $event->getMetadata(),
                    $event->getTimestamp()
                );
            }
        } catch (Exception $e) {
            if ($event->getPayload() instanceof EventExecutionFailed) {
                return;
            }

            $this->publish(new GenericEventMessage(
                new EventExecutionFailed([
                    'exception' => $e,
                    'event'     => $event,
                ]),
                $event->getMetadata()->toArray()
            ));
        }
    }

    /**
     * @param EventMessageInterface $event
     * @return string
     */
    private function getEventName(EventMessageInterface $event)
    {
        $name = $event->getPayloadType();

        $pos = strrpos($name, '\\');
        if ($pos !== false) {
            $name = substr($name, $pos + 1);
        }

        if (substr($name, -5) == 'Event') {
            $name = substr($name, 0, -5);
        }

        return $name;
    }
}
