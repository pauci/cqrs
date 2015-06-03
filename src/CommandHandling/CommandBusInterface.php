<?php

namespace CQRS\CommandHandling;

interface CommandBusInterface
{
    /**
     * Dispatches the command to appropriate command handler
     *
     * @param object $command
     */
    public function handle($command);
}
