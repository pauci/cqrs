<?php

declare(strict_types=1);

namespace CQRS\EventStream;

use Ramsey\Uuid\UuidInterface;
use Traversable;

interface EventStreamInterface extends Traversable
{
    public function getLastEventId(): ?UuidInterface;
}
