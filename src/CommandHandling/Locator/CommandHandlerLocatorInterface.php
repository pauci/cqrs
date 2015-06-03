<?php

namespace CQRS\CommandHandling\Locator;

interface CommandHandlerLocatorInterface
{
    /**
     * @param object $command
     * @return object
     * @throws \CQRS\Exception\RuntimeException
     */
    public function getCommandHandler($command);
}
