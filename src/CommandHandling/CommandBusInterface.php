<?php

declare(strict_types=1);

namespace CQRS\CommandHandling;

interface CommandBusInterface
{
    /**
     * Dispatches command to the appropriate command handler
     */
    public function dispatch(object $command): void;
}
