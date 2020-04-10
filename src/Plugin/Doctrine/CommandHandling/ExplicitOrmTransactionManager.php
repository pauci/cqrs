<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\CommandHandling;

class ExplicitOrmTransactionManager extends AbstractOrmTransactionManager
{
    public function begin(): void
    {
        $this->entityManager->beginTransaction();
    }

    public function commit(): void
    {
        $this->entityManager->flush();
        $this->entityManager->commit();
    }

    public function rollback(): void
    {
        $this->entityManager->rollback();
        $this->entityManager->close();
    }
}
