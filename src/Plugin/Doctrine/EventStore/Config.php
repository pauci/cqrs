<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\EventStore;

final class Config
{
    private string $tableName;

    public function __construct(string $tableName = 'cqrs_event')
    {
        $this->tableName = $tableName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }
}
