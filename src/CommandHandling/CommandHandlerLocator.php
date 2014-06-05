<?php

namespace CQRS\CommandHandling;

interface CommandHandlerLocator
{
    /**
     * @param Command $command
     * @return object
     * @throws \CQRS\Exception\RuntimeException
     */
    public function getCommandHandler(Command $command);
}
