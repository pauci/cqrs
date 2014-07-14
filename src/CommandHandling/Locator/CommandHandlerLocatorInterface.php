<?php

namespace CQRS\CommandHandling\Locator;

use CQRS\CommandHandling\CommandInterface;

interface CommandHandlerLocatorInterface
{
    /**
     * @param CommandInterface $command
     * @return object
     * @throws \CQRS\Exception\RuntimeException
     */
    public function getCommandHandler(CommandInterface $command);
}
