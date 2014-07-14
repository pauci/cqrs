<?php

namespace CQRS\CommandHandling;

interface CommandBusInterface
{
    /**
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command);
}
