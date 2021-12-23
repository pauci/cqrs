<?php

declare(strict_types=1);

namespace CQRS\CommandHandling;

interface CommandHandlerLocatorInterface
{
    public function get(string $commandType): callable;
}
