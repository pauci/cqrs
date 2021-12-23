<?php

declare(strict_types=1);

namespace CQRS\EventHandling;

use CQRS\Domain\Message\EventMessageInterface;

interface EventBusInterface
{
    public function publish(EventMessageInterface $event): void;
}
