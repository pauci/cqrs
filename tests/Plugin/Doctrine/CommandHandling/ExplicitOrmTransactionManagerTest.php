<?php

declare(strict_types=1);

namespace CQRSTest\Plugin\Doctrine\CommandHandling;

use CQRS\Plugin\Doctrine\CommandHandling\ExplicitOrmTransactionManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ExplicitOrmTransactionManagerTest extends TestCase
{
    public function testBeginTransaction(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('beginTransaction');

        /** @var EntityManagerInterface $entityManager */
        $transactionManager = new ExplicitOrmTransactionManager($entityManager);

        $transactionManager->begin();
    }

    public function testCommitTransaction(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('flush');
        $entityManager->expects($this->once())
            ->method('commit');

        /** @var EntityManagerInterface $entityManager */
        $transactionManager = new ExplicitOrmTransactionManager($entityManager);

        $transactionManager->commit();
    }

    public function testRollbackTransaction(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('rollback');
        $entityManager->expects($this->once())
            ->method('close');

        /** @var EntityManagerInterface $entityManager */
        $transactionManager = new ExplicitOrmTransactionManager($entityManager);

        $transactionManager->rollback();
    }
}
