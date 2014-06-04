<?php

namespace CQRS\CommandHandling;

use CQRS\Exception\RuntimeException;

class MemoryCommandHandlerLocator implements CommandHandlerLocator
{
    /** @var array */
    private $handlers = [];

    /**
     * @param Command $command
     * @return object
     * @throws RuntimeException
     */
    public function getCommandHandler(Command $command)
    {
        $commandType = new CommandType($command);
        $key = strtolower($commandType);

        if (!isset($this->handlers[$key])) {
            throw new RuntimeException(sprintf("No service registered for command type '%s'", $commandType));
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
            throw new RuntimeException(sprintf("No valid service given for command type '%s'", $commandType));
        }

        $this->handlers[strtolower($commandType)] = $service;
    }
} 
