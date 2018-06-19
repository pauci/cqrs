<?php
declare(strict_types=1);

namespace CQRS\Domain\Model;

trait DeletableTrait
{
    /**
     * @var bool
     */
    private $deleted = false;

    /**
     * Marks this aggregate as deleted, instructing a Repository to remove that aggregate at an appropriate time
     */
    protected function markAsDeleted(): void
    {
        $this->deleted = true;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }
}
