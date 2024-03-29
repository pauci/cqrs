<?php

declare(strict_types=1);

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\DomainEventMessageInterface;

interface AggregateRootInterface
{
    /**
     * Returns the identifier of this aggregate.
     *
     * @return mixed
     */
    public function getId(): mixed;

    /**
     * Returns the events in the aggregate that have been raised since creation or the last commit.
     *
     * @return DomainEventMessageInterface[]
     */
    public function getUncommittedEvents(): array;

    /**
     * Returns the number of uncommitted events currently available in the aggregate.
     */
    public function getUncommittedEventsCount(): int;

    /**
     * Clears the events currently marked as "uncommitted".
     */
    public function commitEvents(): void;

    /**
     * Returns the current version number of the aggregate, or null if the aggregate is newly created.
     * This version must reflect the version number of the aggregate on which changes are applied.
     *
     * Each time the aggregate is modified and stored in a repository, the version number must be increased by
     * at least 1. This version number can be used by optimistic locking strategies and detection of conflicting
     * concurrent modification.
     */
    public function getVersion(): int;
}
