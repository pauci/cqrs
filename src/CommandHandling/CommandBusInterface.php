<?php

namespace CQRS\CommandHandling;

interface CommandBusInterface
{
    /**
     * Dispatches command to the appropriate command handler
     *
     * @param object $command
     */
    public function dispatch($command);
}
