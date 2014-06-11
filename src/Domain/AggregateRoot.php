<?php

namespace CQRS\Domain;

use CQRS\Exception\RuntimeException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @property-read AbstractId $id
 */
abstract class AggregateRoot
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity = "AbstractId")
     * @ORM\JoinColumn(name = "id")
     * @var AbstractId
     */
    private $id;

    /** @var DomainEvent[] */
    private $events = [];

    /**
     * @param DomainEvent $event
     */
    public function raiseDomainEvent(DomainEvent $event)
    {
        $this->events[] = $event;
    }

    /**
     * @return DomainEvent[]
     */
    public function pullDomainEvents()
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    /**
     * @param string $name
     * @return AbstractId
     * @throws \RuntimeException
     */
    public function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->id;
        }

        throw new RuntimeException(sprintf('Trying to access invalid property "%s"', $name));
    }
}
