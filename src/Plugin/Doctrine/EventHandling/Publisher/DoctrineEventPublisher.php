<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\EventHandling\Publisher;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventHandling\Publisher\SimpleEventPublisher;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;

class DoctrineEventPublisher extends SimpleEventPublisher implements EventSubscriber
{
    /**
     * @var EventMessageInterface[]
     */
    private array $events = [];

    public function getSubscribedEvents(): array
    {
        return [
            Events::preFlush,
            Events::postFlush,
        ];
    }

    public function publishEvents(): void
    {
        $this->events = array_merge($this->events, $this->dequeueEvents());
        // Actual event dispatching is postponed until doctrine's postFlush event.
    }

    public function preFlush(): void
    {
        $this->publishEvents();
    }

    public function postFlush(): void
    {
        if (empty($this->events)) {
            return;
        }

        $events = $this->events;
        $this->events = [];
        $this->dispatchEvents($events);
    }
}
