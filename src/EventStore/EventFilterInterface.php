<?php

namespace CQRS\EventStore;

use CQRS\Domain\Message\EventMessageInterface;

interface EventFilterInterface
{
    public function isValid(EventMessageInterface $event);
}
