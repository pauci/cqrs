<?php

namespace CQRS\EventHandling\Locator;

use CQRS\Exception\RuntimeException;
use CQRS\Exception\InvalidArgumentException;

/**
 * In Memory Event Handler Locator
 *
 * You can register Event handlers and every method starting
 * with "on" will be registered as handling an event.
 *
 * By convention the part after the "on" matches the event name.
 * Comparisons are done in lower-case.
 */
class MemoryEventHandlerLocator implements EventHandlerLocatorInterface
{
    /**
     * @var array
     */
    private $handlers = [];

    /**
     * @var array
     */
    private $regexpHandlers = [];

    /**
     * @var array
     */
    private $globalHandlers = [];

    /**
     * @param string $eventName
     * @return callable[]
     */
    public function getEventHandlers($eventName)
    {
        $eventName = strtolower($eventName);

        $handlersByPriority = [];
        if (isset($this->handlers[$eventName])) {
            $handlersByPriority = $this->handlers[$eventName];
        }

        foreach ($this->regexpHandlers as $regexp => $handlers) {
            if (preg_match($regexp, $eventName)) {
                $handlersByPriority = array_merge_recursive($handlersByPriority, $handlers);
            }
        }

        if (empty($handlersByPriority)) {
            return [];
        }

        // Sort handlers by priority, highest first
        krsort($handlersByPriority);

        return call_user_func_array('array_merge', $handlersByPriority);
    }

    /**
     * @param string|array $events   The event(s) to listen to.
     * @param callable     $listener The listener callback.
     * @param int          $priority
     */
    public function addListener($events, callable $listener, $priority = 1)
    {
        foreach ((array) $events as $event) {
            $eventName = strtolower($event);

            if (!preg_match('/^[\*a-z0-9_-]+$/', $eventName)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid event name "%s"',
                    $event
                ));
            }

            if ($eventName == '*') {
                $this->globalHandlers[$priority][] = $listener;
                continue;
            }

            if (strpos($eventName, '*') !== false) {
                $regexp = '/' . strtr($eventName, ['*' => '[a-z0-9_-]*']) . '/';
                $this->regexpHandlers[$regexp][$priority][] = $listener;
                continue;
            }

            $this->handlers[$eventName][$priority][] = $listener;
        }
    }

    public function removeListener(callable $listener)
    {
        foreach ($this->handlers as $eventName => $eventHandlers) {
            foreach ($eventHandlers as $priority => $priorityHandlers) {
                foreach ($priorityHandlers as $key => $handler) {
                    if ($handler === $listener) {
                        unset($this->handlers[$eventName][$priority][$key]);
                    }
                }
            }
        }
    }

    /**
     * @param object $subscriber The subscriber object.
     * @param int    $priority
     * @throws RuntimeException
     */
    public function addSubscriber($subscriber, $priority = 1)
    {
        if (!is_object($subscriber)) {
            throw new RuntimeException(sprintf(
                'No valid event subscriber given; expected object, got %s',
                gettype($subscriber)
            ));
        }

        $listeners = [];
        foreach (get_class_methods($subscriber) as $methodName) {
            if (strpos($methodName, 'on') !== 0) {
                continue;
            }

            $eventName = strtolower(substr($methodName, 2));
            $listeners[$eventName] = [$subscriber, $methodName];
        }

        if (empty($listeners)) {
            throw new RuntimeException(sprintf(
                'Event subscriber %s does not contain any event listening methods',
                get_class($subscriber)
            ));
        }

        foreach ($listeners as $eventName => $listener) {
            $this->addListener($eventName, $listener, $priority);
        }
    }
}
