<?php

namespace CQRS\EventHandling\Publisher;

use CQRS\Domain\Model\AggregateRootInterface;

class SimpleIdentityMap implements IdentityMapInterface
{
    /**
     * @var AggregateRootInterface[]
     */
    private $aggregateRoots = [];

    /**
     * @param AggregateRootInterface $aggregateRoot
     */
    public function add(AggregateRootInterface $aggregateRoot)
    {
        $this->aggregateRoots[] = $aggregateRoot;
    }

    /**
     * @return AggregateRootInterface[]
     */
    public function all()
    {
        return $this->aggregateRoots;
    }
}
