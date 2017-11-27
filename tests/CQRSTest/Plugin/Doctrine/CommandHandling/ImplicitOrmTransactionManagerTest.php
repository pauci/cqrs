<?php

namespace CQRSTest\Plugin\Doctrine\CommandHandling;

use CQRS\Plugin\Doctrine\CommandHandling\ImplicitOrmTransactionManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ImplicitOrmTransactionManagerTest extends TestCase
{
    public function testBeginTransaction()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())
            ->method('beginTransaction');

        /** @var EntityManagerInterface $entityManager */
        $transactionManager = new ImplicitOrmTransactionManager($entityManager);

        $transactionManager->begin();
    }

    public function testCommitTransaction()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('flush');
        $entityManager->expects($this->never())
            ->method('commit');

        /** @var EntityManagerInterface $entityManager */
        $transactionManager = new ImplicitOrmTransactionManager($entityManager);

        $transactionManager->commit();
    }

    public function testRollbackTransaction()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())
            ->method('rollback');
        $entityManager->expects($this->never())
            ->method('close');

        /** @var EntityManagerInterface $entityManager */
        $transactionManager = new ImplicitOrmTransactionManager($entityManager);

        $transactionManager->rollback();
    }
}

