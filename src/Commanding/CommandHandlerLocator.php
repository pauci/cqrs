<?php

namespace CQRS\Commanding;

interface CommandHandlerLocator
{
    /**
     * @param object $command
     * @return object
     */
    public function getCommandHandler($command);
} 
