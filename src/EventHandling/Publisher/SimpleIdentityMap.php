<?php

declare(strict_types=1);

namespace CQRS\EventHandling\Publisher;

use CQRS\Domain\Model\AggregateRootInterface;

class SimpleIdentityMap implements IdentityMapInterface
{
    /**
     * @var AggregateRootInterface[]
     */
    private array $aggregateRoots = [];

    /**
     * @return AggregateRootInterface[]
     */
    public function getAll(): array
    {
        return array_values($this->aggregateRoots);
    }

    public function add(AggregateRootInterface $aggregateRoot): void
    {
        if (!in_array($aggregateRoot, $this->aggregateRoots, true)) {
            $this->aggregateRoots[] = $aggregateRoot;
        }
    }

    public function remove(AggregateRootInterface $aggregateRoot): void
    {
        $index = array_search($aggregateRoot, $this->aggregateRoots, true);
        if (false !== $index) {
            unset($this->aggregateRoots[$index]);
        }
    }

    public function clear(): void
    {
        $this->aggregateRoots = [];
    }
}
