<?php

namespace CQRS\Domain\Message;

use CQRS\Domain\Model\AggregateRootInterface;

abstract class AbstractDomainEvent extends AbstractEvent implements DomainEventInterface
{
    /** @var AggregateRootInterface */
    private $aggregate;

    /** @var string */
    private $aggregateType;

    /** @var mixed */
    private $aggregateId;

    /**
     * @param array $data
     * @param AggregateRootInterface $aggregate
     */
    public function __construct(array $data = [], AggregateRootInterface $aggregate = null)
    {
        parent::__construct($data);

        if ($aggregate !== null) {
            $this->setAggregate($aggregate);
        }
    }

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
     * @return mixed
     */
    public function getAggregateId()
    {
        if ($this->aggregateId === null && $this->aggregate !== null) {
            $this->aggregateId = $this->aggregate->getId();
        }
        return $this->aggregateId;
    }
}
