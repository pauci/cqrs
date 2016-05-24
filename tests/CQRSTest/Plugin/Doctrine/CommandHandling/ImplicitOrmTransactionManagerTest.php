<?php

namespace CQRSTest\Plugin\Doctrine\CommandHandling;

use CQRS\Plugin\Doctrine\CommandHandling\ImplicitOrmTransactionManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit_Framework_TestCase;

class ImplicitOrmTransactionManagerTest extends PHPUnit_Framework_TestCase
{
    public function testBeginTransaction()
    {
        $entityManager = $this->getMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())
            ->method('beginTransaction');

        /** @var EntityManagerInterface $entityManager */
        $transactionManager = new ImplicitOrmTransactionManager($entityManager);

        $transactionManager->begin();
    }

    public function testCommitTransaction()
    {
        $entityManager = $this->getMock(EntityManagerInterface::class);
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
        $entityManager = $this->getMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())
            ->method('rollback');
        $entityManager->expects($this->never())
            ->method('close');

        /** @var EntityManagerInterface $entityManager */
        $transactionManager = new ImplicitOrmTransactionManager($entityManager);

        $transactionManager->rollback();
    }
}

