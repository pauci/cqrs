<?php

namespace CQRS\Domain\SuperType;

/**
 * @property-read int $id
 */
abstract class AggregateRoot extends Entity
{
    /** @var AbstractDomainEvent[] */
    private $events = [];

    /**
     * @param AbstractDomainEvent $event
     */
    public function raiseDomainEvent(AbstractDomainEvent $event)
    {
        $this->events[] = $event;
    }

    /**
     * @return AbstractDomainEvent[]
     */
    public function pullDomainEvents()
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    /**
     * @param string $name
     * @return int
     * @throws \RuntimeException
     */
    public function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->getId();
        }

        throw new \RuntimeException(sprintf('Trying to access invalid property "%s"', $name));
    }
}
