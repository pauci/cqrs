<?php

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\DomainEventMessageInterface;

interface AggregateRootInterface
{
    /**
     * Returns the identifier of this aggregate.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Returns the events in the aggregate that have been raised since creation or the last commit.
     *
     * @return DomainEventMessageInterface[]
     */
    public function getUncommittedEvents();

    /**
     * Returns the number of uncommitted events currently available in the aggregate.
     *
     * @return int
     */
    public function getUncommittedEventsCount();

    /**
     * Clears the events currently marked as "uncommitted".
     */
    public function commitEvents();

    /**
     * Returns the current version number of the aggregate, or null if the aggregate is newly created.
     * This version must reflect the version number of the aggregate on which changes are applied.
     *
     * Each time the aggregate is modified and stored in a repository, the version number must be increased by
     * at least 1. This version number can be used by optimistic locking strategies and detection of conflicting
     * concurrent modification.
     *
     * @return int
     */
    public function getVersion();

    /**
     * Indicates whether this aggregate has been marked as deleted. When true, it is an instruction to the repository
     * to remove this instance at an appropriate time.
     *
     * Repositories should not return any instances of Aggregates that return true on isDeleted().
     *
     * @return bool
     */
    public function isDeleted();
}
