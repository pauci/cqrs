<?php

namespace CQRS\EventStream;

use Ramsey\Uuid\UuidInterface;
use Traversable;

interface EventStreamInterface extends Traversable
{
    /**
     * @return UuidInterface|null
     */
    public function getLastEventId();
}
