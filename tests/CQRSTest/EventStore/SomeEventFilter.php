<?php

namespace CQRSTest\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventStore\EventFilterInterface;

class SomeEventFilter implements EventFilterInterface
{
    public function isValid(EventMessageInterface $event): bool
    {
        $meta = $event->getMetadata();
        return (bool) ($meta['valid'] ?? false);
    }
}
