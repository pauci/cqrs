<?php

namespace CQRS\CommandHandling;

/**
 * @codeCoverageIgnore
 */
class NoTransactionManager implements TransactionManager
{
    public function begin()
    {}

    public function commit()
    {}

    public function rollback()
    {}
} 
