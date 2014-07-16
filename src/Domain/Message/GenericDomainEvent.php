<?php

namespace CQRS\Domain\Message;

use CQRS\Domain\Model\AggregateRootInterface;

class GenericDomainEvent extends AbstractDomainEvent
{
    /**
     * @param string $eventName
     * @param array $data
     * @param AggregateRootInterface $aggregate
     */
    public function __construct($eventName, array $data = [], AggregateRootInterface $aggregate = null)
    {
        $this->setEventName($eventName);

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        parent::__construct([], $aggregate);
    }
}
