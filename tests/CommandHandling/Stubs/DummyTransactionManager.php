<?php

declare(strict_types=1);

namespace CQRSTest\CommandHandling\Stubs;

use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;

class DummyTransactionManager implements TransactionManagerInterface
{
    public int $begin = 0;

    public int $commit = 0;

    public int $rollback = 0;

    public function begin(): void
    {
        $this->begin++;
    }

    public function commit(): void
    {
        $this->commit++;
    }

    public function rollback(): void
    {
        $this->rollback++;
    }
}
