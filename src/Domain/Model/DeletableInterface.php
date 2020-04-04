<?php

declare(strict_types=1);

namespace CQRS\Domain\Model;

interface DeletableInterface
{
    /**
     * Indicates whether this aggregate has been marked as deleted. When true, it is an instruction to the repository
     * to remove this instance at an appropriate time.
     *
     * Repositories should not return any instances of Aggregates that return true on isDeleted().
     *
     * @return bool
     */
    public function isDeleted(): bool;
}
