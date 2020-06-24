<?php

declare(strict_types=1);

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\Metadata;
use CQRS\Exception\RuntimeException;
use Doctrine\ORM\Mapping as ORM;

trait AggregateRootTrait
{
    private ?EventContainer $eventContainer = null;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private ?int $lastEventSequenceNumber = null;

    /**
     * @return mixed
     */
    abstract public function getId();

    /**
     * Registers an event to be published when the aggregate is saved, containing the given payload and optional
     * metadata.
     *
     * @param Metadata|array $metadata
     */
    protected function registerEvent(object $payload, $metadata = null): DomainEventMessageInterface
    {
        return $this->getEventContainer()
            ->addEvent($payload, $metadata);
    }

    protected function registerEventMessage(DomainEventMessageInterface $eventMessage): DomainEventMessageInterface
    {
        return $this->getEventContainer()
            ->addEventMessage($eventMessage);
    }

    /**
     * {@inheritdoc}
     *
     * @return DomainEventMessageInterface[]
     */
    public function getUncommittedEvents(): array
    {
        if ($this->eventContainer === null) {
            return [];
        }
        return $this->eventContainer->getEvents();
    }

    /**
     * {@inheritdoc}
     */
    public function getUncommittedEventsCount(): int
    {
        return $this->eventContainer ? count($this->eventContainer) : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function commitEvents(): void
    {
        if ($this->eventContainer !== null) {
            $this->lastEventSequenceNumber = $this->eventContainer->getLastSequenceNumber();
            $this->eventContainer->commit();
        }
    }

    /**
     * @throws RuntimeException
     */
    private function getEventContainer(): EventContainer
    {
        if ($this->eventContainer === null) {
            $aggregateId = $this->getId();
            $aggregateType = get_class($this);
            if ($aggregateId === null) {
                throw new RuntimeException(sprintf(
                    'Aggregate ID is unknown in %s. '
                    . 'Make sure the Aggregate ID is initialized before registering events.',
                    $aggregateType
                ));
            }

            // @todo Na zaciatku nastavit sequence number na 0 = nula eventov...
            $this->eventContainer = new EventContainer($aggregateType, $aggregateId);
            $this->eventContainer->initializeSequenceNumber($this->lastEventSequenceNumber);
        }
        return $this->eventContainer;
    }
}
