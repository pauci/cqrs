<?php

namespace CQRS\Domain\Payload;

use CQRS\Exception\RuntimeException;

/**
 * @property-read mixed $aggregateId
 */
abstract class AbstractDomainEvent extends AbstractEvent
{
    /**
     * @var mixed
     */
    protected $aggregateId;

    /**
     * This method should by called only during the registration of an event
     *
     * @param mixed $aggregateId
     * @throws RuntimeException
     */
    public function setAggregateId($aggregateId)
    {
        if (null !== $this->aggregateId) {
            throw new RuntimeException('Aggregate ID has been already assigned to this event');
        }
        $this->aggregateId = $aggregateId;
    }
}
