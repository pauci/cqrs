<?php

declare(strict_types=1);

namespace CQRSTest\CommandHandling\Stubs;

use CQRS\EventHandling\Publisher\EventPublisherInterface;

class DummyEventPublisher implements EventPublisherInterface
{
    public int $published = 0;

    public function publishEvents(): void
    {
        $this->published++;
    }
}
