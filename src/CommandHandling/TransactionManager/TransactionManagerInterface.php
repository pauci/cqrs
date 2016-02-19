<?php

namespace CQRS\CommandHandling\TransactionManager;

interface TransactionManagerInterface
{
    public function begin();

    public function commit();

    public function rollback();
}
