<?php

declare(strict_types=1);

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;

interface EventFilterInterface
{
    public function isValid(EventMessageInterface $event): bool;
}
