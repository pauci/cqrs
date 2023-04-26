<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\CommandHandling;

use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractOrmTransactionManager implements TransactionManagerInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    ) {
    }
}
