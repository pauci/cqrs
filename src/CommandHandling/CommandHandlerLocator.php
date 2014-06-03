<?php

namespace CQRS\CommandHandling;

interface CommandHandlerLocator
{
    /**
     * @param Command $command
     * @return object
     */
    public function getCommandHandler(Command $command);
}
