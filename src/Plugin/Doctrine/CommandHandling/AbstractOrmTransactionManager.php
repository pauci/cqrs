<?php

namespace CQRS\Plugin\Doctrine\CommandHandling;

use CQRS\CommandHandling\TransactionManager;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractOrmTransactionManager implements TransactionManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
