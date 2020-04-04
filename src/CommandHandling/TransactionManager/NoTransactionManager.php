<?php

declare(strict_types=1);

namespace CQRS\CommandHandling\TransactionManager;

/**
 * @codeCoverageIgnore
 */
class NoTransactionManager implements TransactionManagerInterface
{
    public function begin(): void
    {
    }

    public function commit(): void
    {
    }

    public function rollback(): void
    {
    }
}
