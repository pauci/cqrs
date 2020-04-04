<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\CommandHandling;

class ImplicitOrmTransactionManager extends AbstractOrmTransactionManager
{
    public function begin(): void
    {
    }

    public function commit(): void
    {
        $this->entityManager->flush();
    }

    public function rollback(): void
    {
    }
}
