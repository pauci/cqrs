<?php

namespace CQRS\Plugin\Doctrine\CommandHandling;

use CQRS\CommandHandling\TransactionManager;
use Doctrine\ORM\EntityManager;

abstract class AbstractOrmTransactionManager implements TransactionManager
{
    /** @var EntityManager */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
