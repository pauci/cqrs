<?php

declare(strict_types=1);

namespace CQRS\EventHandling\Publisher;

use CQRS\Domain\Model\AggregateRootInterface;

interface IdentityMapInterface
{
    /**
     * @return AggregateRootInterface[]
     */
    public function getAll(): array;

    public function add(AggregateRootInterface $aggregateRoot): void;

    public function remove(AggregateRootInterface $aggregateRoot): void;

    public function clear(): void;
}
