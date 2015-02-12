<?php

namespace CQRSTest\EventStore;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventStore\EventFilterInterface;

class SomeEventFilter implements EventFilterInterface
{
    public function isValid(EventMessageInterface $event)
    {
        $meta = $event->getMetadata();
        return isset($meta['valid']) ? (bool) $meta['valid'] : false;
    }
}
