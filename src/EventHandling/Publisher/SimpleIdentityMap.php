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
     * @param mixed $id
     * @return AggregateRootInterface|null
     */
    public function get($id)
    {
        $key = (string) $id;
        return $this->aggregateRoots[$key];
    }

    /**
     * @return AggregateRootInterface[]
     */
    public function getAll()
    {
        return $this->aggregateRoots;
    }

    /**
     * @param AggregateRootInterface $aggregateRoot
     */
    public function add(AggregateRootInterface $aggregateRoot)
    {
        $key = (string) $aggregateRoot->getId();
        $this->aggregateRoots[$key] = $aggregateRoot;
    }

    /**
     * @param AggregateRootInterface $aggregateRoot
     */
    public function remove(AggregateRootInterface $aggregateRoot)
    {
        $key = (string) $aggregateRoot->getId();
        unset($this->aggregateRoots[$key]);
    }

    public function clear()
    {
        $this->aggregateRoots = [];
    }
}
