<?php

namespace CQRS\Domain\SuperType;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use CQRS\Domain\SuperType\AbstractDomainEvent;

/**
 * @ORM\MappedSuperclass
 * @property-read int $id
 */
abstract class AggregateRoot extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type = "integer")
     * @var int
     */
    private $id;

    /** @var AbstractDomainEvent[] */
    private $events = [];

    /**
     * @return AbstractDomainEvent[]
     */
    public function pullDomainEvents()
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    public function id()
    {
        return parent::id();
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
                return $this->id;
        }

        throw new \RuntimeException(sprintf('Trying to access invalid property "%s"', $name));
    }

    /**
     * @param AbstractDomainEvent $event
     */
    protected function raiseDomainEvent(AbstractDomainEvent $event)
    {
        $event->aggregateType = $this->getAggregateType();
        $event->aggregateId   = &$this->id();
        $event->occurredAt    = $this->getMicrosecondsNow();

        $this->events[] = $event;
    }

    /**
     * @return string
     */
    private function getAggregateType()
    {
        $class = get_class();
        $pos   = strrpos($class, '\\');

        if ($pos === false) {
            return $class;
        }

        return substr($class, $pos + 1);
    }

    /**
     * @return DateTime
     */
    private function getMicrosecondsNow()
    {
        return DateTime::createFromFormat('u', substr(microtime(), 2, 6));
    }
}
