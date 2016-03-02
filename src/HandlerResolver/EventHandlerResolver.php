<?php

namespace CQRS\HandlerResolver;

class EventHandlerResolver
{
    /**
     * @param mixed $handler
     * @param string $eventType
     * @return callable
     */
    public function __invoke($handler, $eventType)
    {
        if (is_object($handler) && !is_callable($handler)) {
            $method = $this->resolveHandlingMethod($eventType);
            if (method_exists($handler, $method)) {
                return [$handler, $method];
            }
        }

        return $handler;
    }

    /**
     * Derives event handling method name from event type
     *
     * @param string $eventType
     * @return string
     */
    protected function resolveHandlingMethod($eventType)
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
