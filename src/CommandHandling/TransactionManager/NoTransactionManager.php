<?php

namespace CQRS\CommandHandling\TransactionManager;

/**
 * @codeCoverageIgnore
 */
class NoTransactionManagerInterface implements TransactionManagerInterface
{
    public function begin()
    {}

    public function commit()
    {}

    public function rollback()
    {}
} 
