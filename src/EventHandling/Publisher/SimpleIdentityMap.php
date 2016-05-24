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
     * @return AggregateRootInterface[]
     */
    public function getAll()
    {
        return array_values($this->aggregateRoots);
    }

    /**
     * @param AggregateRootInterface $aggregateRoot
     */
    public function add(AggregateRootInterface $aggregateRoot)
    {
        if (!in_array($aggregateRoot, $this->aggregateRoots, true)) {
            $this->aggregateRoots[] = $aggregateRoot;
        }
    }

    /**
     * @param AggregateRootInterface $aggregateRoot
     */
    public function remove(AggregateRootInterface $aggregateRoot)
    {
        $index = array_search($aggregateRoot, $this->aggregateRoots, true);
        if (false !== $index) {
            unset($this->aggregateRoots[$index]);
        }
    }

    public function clear()
    {
        $this->aggregateRoots = [];
    }
}
