<?php

namespace CQRS\Domain\Message;

use CQRS\Domain\Model\AggregateRootInterface;
use Rhumsaa\Uuid\Uuid;

interface DomainEventMessageInterface extends EventMessageInterface
{
    /**
     * @param AggregateRootInterface $aggregate
     * @return self
     */
    public function setAggregate(AggregateRootInterface $aggregate);

    /**
     * @return string
     */
    public function getAggregateType();

    /**
     * @return Uuid|int
     */
    public function getAggregateId();
}
