<?php

namespace CQRS\Domain;

use CQRS\Exception\RuntimeException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @property-read Id $id
 */
abstract class AggregateRoot
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity = "Id")
     * @ORM\JoinColumn(name = "id")
     * @var Id
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
     * @return Id
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
