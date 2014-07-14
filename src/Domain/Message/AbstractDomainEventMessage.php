<?php

namespace CQRS\Domain\Message;

use CQRS\Domain\Model\AggregateRootInterface;
use Rhumsaa\Uuid\Uuid;

abstract class AbstractDomainEventMessage extends AbstractEventMessage implements DomainEventMessageInterface
{
    /** @var AggregateRootInterface */
    private $aggregate;

    /** @var string */
    private $aggregateType;

    /** @var Uuid|int */
    private $aggregateId;

    /**
     * @param AggregateRootInterface $aggregate
     * @return $this
     */
    public function setAggregate(AggregateRootInterface $aggregate)
    {
        $this->aggregate     = $aggregate;
        $this->aggregateType = get_class($aggregate);
        $this->aggregateId   = $aggregate->getId();
        return $this;
    }

    /**
     * @return string
     */
    public function getAggregateType()
    {
        return $this->aggregateType;
    }

    /**
     * @return Uuid|int
     */
    public function getAggregateId()
    {
        if ($this->aggregateId === null && $this->aggregate !== null) {
            $this->aggregateId = $this->aggregate->getId();
        }
        return $this->aggregateId;
    }
}
