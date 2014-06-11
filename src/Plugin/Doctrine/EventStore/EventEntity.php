<?php

namespace CQRS\Plugin\Doctrine\EventStore;

use CQRS\EventHandling\DefaultDomainEvent;
use Rhumsaa\Uuid\Uuid;

/**
 * @ORM\Entity
 */
class EventEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type = "Uuid")
     * @var Uuid
     */
    private $id;

    /**
     * @ORM\Column(length = 200)
     * @var string
     */
    private $aggregateType;

    /**
     * @ORM\Column(type = "integer")
     * @var int
     */
    private $aggregateId;

    /**
     * @ORM\Column(type = "object")
     * @var DefaultDomainEvent
     */
    private $event;

    /**
     * @var \DateTime
     */
    private $occurredAt;

    /**
     * @param DefaultDomainEvent $event
     */
    public function __construct(DefaultDomainEvent $event)
    {
        $this->id         = $event->id;
        $this->event      = $event;
        $this->occurredAt = $event->occurredAt;
    }
}
