<?php

namespace CQRS\Commanding;

interface CommandBus
{
    /**
     * @param object $command
     */
    public function handle($command);
}
