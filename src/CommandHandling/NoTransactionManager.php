<?php

namespace CQRS\CommandHandling;

class NoTransactionManager implements TransactionManager
{
    public function begin()
    {}

    public function commit()
    {}

    public function rollback()
    {}
} 
