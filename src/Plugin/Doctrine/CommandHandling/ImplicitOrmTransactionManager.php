<?php

namespace CQRS\Plugin\Doctrine\CommandHandling;

class ImplicitTransactionManager extends AbstractOrmTransactionManager
{
    public function begin()
    {}

    public function commit()
    {
        $this->entityManager->flush();
    }

    public function rollback()
    {}
} 
