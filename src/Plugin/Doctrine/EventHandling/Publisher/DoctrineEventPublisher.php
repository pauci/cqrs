<?php

namespace CQRS\Plugin\Doctrine\EventHandling\Publisher;

use CQRS\EventHandling\Publisher\SimpleEventPublisher;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;

class DoctrineEventPublisher extends SimpleEventPublisher implements EventSubscriber
{
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postFlush
        ];
    }

    public function publishEvents()
    {
        // Do nothing. Actual event publishing occurs on doctrine's postFlush event.
    }

    public function postFlush()
    {
        parent::publishEvents();
    }
}