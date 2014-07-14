<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventHandling\Locator\EventHandlerLocatorInterface;
use Exception;

class SynchronousEventBus implements EventBusInterface
{
    /** @var EventHandlerLocatorInterface */
    private $locator;

    /**
     * @param EventHandlerLocatorInterface $locator
     */
    public function __construct(EventHandlerLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param EventMessageInterface $event
     */
    public function publish(EventMessageInterface $event)
    {
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
