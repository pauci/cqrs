<?php

declare(strict_types=1);

namespace CQRS\HandlerResolver;

class CommandHandlerResolver
{
    /**
     * @param mixed $handler
     */
    public function __invoke($handler, string $commandType): callable
    {
        if (is_object($handler) && !is_callable($handler)) {
            $method = $this->resolveHandlingMethod($commandType);
            $callback = [$handler, $method];
            if (is_callable($callback)) {
                return $callback;
            }
        }

        return $handler;
    }

    /**
     * Derives command handling method name from event type
     */
    protected function resolveHandlingMethod(string $commandType): string
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
