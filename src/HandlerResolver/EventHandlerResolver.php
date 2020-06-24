<?php

declare(strict_types=1);

namespace CQRS\HandlerResolver;

class EventHandlerResolver
{
    /**
     * @param mixed $handler
     */
    public function __invoke($handler, string $eventType): callable
    {
        if (is_object($handler) && !is_callable($handler)) {
            $method = $this->resolveHandlingMethod($eventType);
            $callback = [$handler, $method];
            if (is_callable($callback)) {
                return $callback;
            }
        }

        return $handler;
    }

    /**
     * Derives event handling method name from event type
     */
    protected function resolveHandlingMethod(string $eventType): string
    {
        // Remove namespace
        $pos = strrpos($eventType, '\\');
        if ($pos !== false) {
            $eventType = substr($eventType, $pos + 1);
        }

        // Remove "Event" suffix
        if (substr($eventType, -5) === 'Event') {
            $eventType = substr($eventType, 0, -5);
        }

        return 'on' . $eventType;
    }
}
