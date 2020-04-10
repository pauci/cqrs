<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\CommandHandling;

use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractOrmTransactionManager implements TransactionManagerInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
