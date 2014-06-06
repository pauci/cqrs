<?php

namespace CQRS\EventHandling;

use CQRS\Exception\RuntimeException;

/**
 * In Memory Event Handler Locator
 *
 * You can register Event handlers and every method starting
 * with "on" will be registered as handling an event.
 *
 * By convention the part after the "on" matches the event name.
 * Comparisons are done in lower-case.
 */
class MemoryEventHandlerLocator implements EventHandlerLocator
{
    /** @var array */
    private $handlers = [];

    /**
     * @param EventName $eventName
     * @return Callable[]
     */
    public function getEventHandlers(EventName $eventName)
    {
        $eventName = strtolower($eventName);

        if (!isset($this->handlers[$eventName])) {
            return [];
        }

        $handlersByPriority = $this->handlers[$eventName];
        // Sort handlers by priority, highest first
        krsort($handlersByPriority);

        return call_user_func_array('array_merge', $handlersByPriority);
    }

    /**
     * @param string|array $eventName
     * @param callable $callback
     * @param int $priority
     */
    public function registerCallback($eventName, Callable $callback, $priority = 1)
    {
        $eventNames = (array) $eventName;

        foreach ($eventNames as $eventName) {
            $eventName = strtolower($eventName);

            $this->handlers[$eventName][$priority][] = $callback;
        }
    }

    /**
     * @param object $subscriber
     * @param int $priority
     * @throws RuntimeException
     */
    public function registerSubscriber($subscriber, $priority = 1)
    {
        if (!is_object($subscriber)) {
            throw new RuntimeException(sprintf(
                'No valid event handler given; expected object, got %s',
                gettype($subscriber)
            ));
        }

        foreach (get_class_methods($subscriber) as $methodName) {
            if (strpos($methodName, 'on') !== 0) {
                continue;
            }

            $eventName = strtolower(substr($methodName, 2));

            $this->registerCallback($eventName, array($subscriber, $methodName), $priority);
        }
    }
}
