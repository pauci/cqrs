<?php

namespace CQRS\CommandHandling\TransactionManager;

/**
 * @codeCoverageIgnore
 */
class NoTransactionManager implements TransactionManagerInterface
{
    public function begin()
    {
    }

    public function commit()
    {
    }

    public function rollback()
    {
    }
}
