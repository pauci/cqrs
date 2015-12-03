<?php

namespace CQRSTest\Plugin\Doctrine\CommandHandling;

use CQRS\Plugin\Doctrine\CommandHandling\ExplicitOrmTransactionManager;
use PHPUnit_Framework_TestCase;

class ExplicitOrmTransactionManagerTest extends PHPUnit_Framework_TestCase
{
    public function testBeginTransaction()
    {
        $entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $entityManager->expects($this->once())
            ->method('beginTransaction');

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $transactionManager = new ExplicitOrmTransactionManager($entityManager);

        $transactionManager->begin();
    }

    public function testCommitTransaction()
    {
        $entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $entityManager->expects($this->once())
            ->method('flush');
        $entityManager->expects($this->once())
            ->method('commit');

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $transactionManager = new ExplicitOrmTransactionManager($entityManager);

        $transactionManager->commit();
    }

    public function testRollbackTransaction()
    {
        $entityManager = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $entityManager->expects($this->once())
            ->method('rollback');
        $entityManager->expects($this->once())
            ->method('close');

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $transactionManager = new ExplicitOrmTransactionManager($entityManager);

        $transactionManager->rollback();
    }
}

