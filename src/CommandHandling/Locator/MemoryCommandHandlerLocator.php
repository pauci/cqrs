<?php

namespace CQRS\CommandHandling\Locator;

use CQRS\Exception\RuntimeException;

class MemoryCommandHandlerLocator implements CommandHandlerLocatorInterface
{
    /** @var array */
    private $handlers = [];

    /**
     * @param object $command
     * @return object
     * @throws RuntimeException
     */
    public function getCommandHandler($command)
    {
        $commandType = get_class($command);
        $key = strtolower($commandType);

        if (!isset($this->handlers[$key])) {
            throw new RuntimeException(sprintf('No service registered for command type "%s"', $commandType));
        }

        return $this->handlers[$key];
    }

    /**
     * @param string $commandType
     * @param object $service
     * @throws RuntimeException
     */
    public function register($commandType, $service)
    {
        if (!is_object($service)) {
            throw new RuntimeException(sprintf(
                'No valid service given for command type "%s"; expected object, got %s',
                $commandType,
                gettype($service)
            ));
        }

        $this->handlers[strtolower($commandType)] = $service;
    }
}
