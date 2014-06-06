<?php

namespace CQRS\EventHandling;

use Exception;

class SynchronousEventBus implements EventBus
{
    /** @var EventHandlerLocator */
    private $locator;

    /**
     * @param EventHandlerLocator $locator
     */
    public function __construct(EventHandlerLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param DomainEvent $event
     */
    public function publish(DomainEvent $event)
    {

        $eventName = new EventName($event);
        $callbacks = $this->locator->getEventHandlers($eventName);

        foreach ($callbacks as $callback) {
            $this->invokeEventHandler($callback, $event);
        }
    }

    /**
     * @param Callable $callback
     * @param DomainEvent $event
     */
    private function invokeEventHandler(Callable $callback, $event)
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
