<?php

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\Domain\Payload\AbstractDomainEvent;
use CQRS\EventHandling\EventInterface;
use CQRS\Exception\RuntimeException;
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
     * Registers an event to be published when the aggregate is saved, containing the given payload and optional
     * metadata.
     *
     * @param EventInterface $payload
     * @param Metadata|array $metadata
     */
    protected function registerEvent(EventInterface $payload, $metadata = null)
    {
        if ($payload instanceof AbstractDomainEvent) {
            $payload->setAggregateId($this->getId());
        }

        $this->getEventContainer()->addEvent($payload, $metadata);
    }

    /**
     * {@inheritdoc}
     *
     * @return GenericDomainEventMessage[]
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
            $id = $this->getId();
            if ($id === null) {
                throw new RuntimeException(sprintf(
                    'Aggregate identifier is unknown in %s. '
                    . 'Make sure the Aggregate identifier is initialized before registering events.',
                    $type
                ));
            }
            $this->eventContainer = new EventContainer($type, $id);
            $this->eventContainer->initializeSequenceNumber($this->lastEventSequenceNumber);
        }
        return $this->eventContainer;
    }
}
