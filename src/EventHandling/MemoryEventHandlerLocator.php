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
    private $handlers = [];

    /**
     * @param EventName $eventName
     * @return array
     */
    public function getEventHandlers(EventName $eventName)
    {
        $eventName = strtolower($eventName);

        if (!isset($this->handlers[$eventName])) {
            return [];
        }

        return $this->handlers[$eventName];
    }

    /**
     * @param object $handler
     * @throws RuntimeException
     */
    public function register($handler)
    {
        if (!is_object($handler)) {
            throw new RuntimeException(sprintf(
                'No valid event handler given; expected object, got %s',
                gettype($handler)
            ));
        }

        foreach (get_class_methods($handler) as $methodName) {
            if (strpos($methodName, 'on') !== 0) {
                continue;
            }

            $eventName = strtolower(substr($methodName, 2));

            if (!isset($this->handlers[$eventName])) {
                $this->handlers[$eventName] = [];
            }

            $this->handlers[$eventName][] = $handler;
        }
    }
}
