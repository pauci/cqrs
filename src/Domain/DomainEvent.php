<?php

namespace CQRS\Domain;

use CQRS\EventHandling\DefaultDomainEvent;
use CQRS\Exception\RuntimeException;

/**
 * @property-read string $aggregateType
 * @property-read int    $aggregateId
 */
abstract class DomainEvent extends DefaultDomainEvent
{
    /** @var string */
    protected $aggregateType;
    /** @var string */
    protected $aggregateId;

    /**
     * @param array $data
     * @throws RuntimeException
     */
    public function __construct(array $data = [])
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $aggregateRoot = isset($backtrace[1]['object'])
            ? $backtrace[1]['object']
            : null;

        if (!$aggregateRoot instanceof AggregateRoot) {
            throw new RuntimeException('DomainEvent can be created only from within an aggregate root');
        }

        $this->setAggregate($aggregateRoot);

        parent::__construct($data);
    }

    /**
     * @param AggregateRoot $aggregate
     */
    private function setAggregate(AggregateRoot $aggregate)
    {
        $this->aggregateType = get_class($aggregate);
        $this->aggregateId   = (string) $aggregate->getId();
    }
}
