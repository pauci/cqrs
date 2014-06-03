<?php

namespace CQRS\CommandHandling;

interface CommandBus
{
    /**
     * @param Command $command
     */
    public function handle(Command $command);
}
