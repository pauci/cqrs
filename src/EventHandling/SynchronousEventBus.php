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
        $services  = $this->locator->getEventHandlers($eventName);

        foreach ($services as $service) {
            $this->invokeEventHandler($service, $eventName, $event);
        }
    }

    /**
     * @param object $service
     * @param EventName $eventName
     * @param DomainEvent $event
     */
    protected function invokeEventHandler($service, $eventName, $event)
    {
        try {
            $methodName = 'on' . $eventName;

            $service->$methodName($event);
        } catch (Exception $e) {
            if ($event instanceof EventExecutionFailed) {
                return;
            }

            $this->publish(new EventExecutionFailed([
                'service'   => get_class($service),
                'exception' => $e,
                'event'     => $event,
            ]));
        }
    }
}
