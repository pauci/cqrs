<?php

namespace CQRSTest\Plugin\Doctrine\CommandHandling;

use CQRS\Plugin\Doctrine\CommandHandling\ImplicitOrmTransactionManager;
use PHPUnit_Framework_TestCase;

class ImplicitOrmTransactionManagerTest extends PHPUnit_Framework_TestCase
{
    public function testBeginTransaction()
    {
        $entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $entityManager->expects($this->never())
            ->method('begin');

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $transactionManager = new ImplicitOrmTransactionManager();
        $transactionManager->setEntityManager($entityManager);

        $transactionManager->begin();
    }

    public function testCommitTransaction()
    {
        $entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $entityManager->expects($this->once())
            ->method('flush');
        $entityManager->expects($this->never())
            ->method('commit');

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $transactionManager = new ImplicitOrmTransactionManager();
        $transactionManager->setEntityManager($entityManager);

        $transactionManager->commit();
    }

    public function testRollbackTransaction()
    {
        $entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $entityManager->expects($this->never())
            ->method('rollback');
        $entityManager->expects($this->never())
            ->method('close');

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $transactionManager = new ImplicitOrmTransactionManager();
        $transactionManager->setEntityManager($entityManager);

        $transactionManager->rollback();
    }
}

