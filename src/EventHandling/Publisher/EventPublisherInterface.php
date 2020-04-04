<?php

declare(strict_types=1);

namespace CQRS\EventHandling\Publisher;

interface EventPublisherInterface
{
    public function publishEvents(): void;
}
