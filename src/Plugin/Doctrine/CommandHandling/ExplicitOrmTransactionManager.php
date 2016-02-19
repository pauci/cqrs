<?php

namespace CQRS\Plugin\Doctrine\CommandHandling;

class ExplicitOrmTransactionManager extends AbstractOrmTransactionManager
{
    public function begin()
    {
        $this->entityManager->beginTransaction();
    }

    public function commit()
    {
        $this->entityManager->flush();
        $this->entityManager->commit();
    }

    public function rollback()
    {
        $this->entityManager->rollback();
        $this->entityManager->close();
    }
}
