<?php

namespace CQRS\HandlerResolver;

class CommandHandlerResolver
{
    /**
     * @param mixed $handler
     * @param string $commandType
     * @return callable
     */
    public function __invoke($handler, $commandType)
    {
        if (is_object($handler) && !is_callable($handler)) {
            $method = $this->resolveHandlingMethod($commandType);
            if (method_exists($handler, $method)) {
                return [$handler, $method];
            }
        }

        return $handler;
    }

    /**
     * Derives command handling method name from event type
     *
     * @param string $commandType
     * @return string
     */
    protected function resolveHandlingMethod($commandType)
    {
        // Remove namespace
        $pos = strrpos($commandType, '\\');
        if ($pos !== false) {
            $commandType = substr($commandType, $pos + 1);
        }

        // Remove "Command" suffix
        if (substr($commandType, -7) === 'Command') {
            $commandType = substr($commandType, 0, -7);
        }

        return lcfirst($commandType);
    }
}
