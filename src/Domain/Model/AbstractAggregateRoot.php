<?php

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\Metadata;
use CQRS\Domain\Payload\AbstractDomainEvent;
use CQRS\EventHandling\EventInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractAggregateRoot implements AggregateRootInterface
{
    /**
     * @var EventContainer
     */
    private $eventContainer;

    /**
     * @var bool
     */
    private $deleted = false;

    /**
     * @ORM\Column(type = "integer", options = {"unsigned" = true})
     * @var int
     */
    private $lastEventSequenceNumber;

    /**
     * @ORM\Version
     * @ORM\Column(type = "integer", options = {"unsigned" = true})
     * @var int
     */
    private $version;

    /**
     * @return mixed
     */
    abstract public function getId();

    /**
     * @return mixed
     */
    abstract protected function &getIdReference();

    /**
     * Registers an event to be published when the aggregate is saved, containing the given payload and optional
     * metadata.
     *
     * @param EventInterface $payload
     * @param Metadata|array $metadata
     * @return DomainEventMessageInterface
     */
    protected function registerEvent(EventInterface $payload, $metadata = null)
    {
        if ($payload instanceof AbstractDomainEvent && null === $payload->aggregateId) {
            $payload->setAggregateId($this->getIdReference());
        }

        return $this->getEventContainer()->addEvent($payload, $metadata);
    }

    /**
     * {@inheritdoc}
     *
     * @return DomainEventMessageInterface[]
     */
    public function getUncommittedEvents()
    {
        if ($this->eventContainer === null) {
            return [];
        }
        return $this->eventContainer->getEvents();
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function getUncommittedEventsCount()
    {
        return count($this->eventContainer);
    }

    /**
     * {@inheritdoc}
     */
    public function commitEvents()
    {
        if ($this->eventContainer !== null) {
            $this->lastEventSequenceNumber = $this->eventContainer->getLastSequenceNumber();
            $this->eventContainer->commit();
        }
    }

    /**
     * Marks this aggregate as deleted, instructing a Repository to remove that aggregate at an appropriate time
     */
    protected function markAsDeleted()
    {
        $this->deleted = true;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return EventContainer
     */
    private function getEventContainer()
    {
        if ($this->eventContainer === null) {
            $type = get_class($this);
            $id   = &$this->getIdReference();
            $this->eventContainer = new EventContainer($type, $id);
            $this->eventContainer->initializeSequenceNumber($this->lastEventSequenceNumber);
        }
        return $this->eventContainer;
    }
}
