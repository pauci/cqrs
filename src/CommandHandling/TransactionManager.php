<?php

namespace CQRS\CommandHandling;

interface TransactionManager
{
    public function begin();

    public function commit();

    public function rollback();
} 
