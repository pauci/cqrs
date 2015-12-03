<?php

namespace CQRS\Plugin\Doctrine\CommandHandling;

use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractOrmTransactionManager implements TransactionManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
