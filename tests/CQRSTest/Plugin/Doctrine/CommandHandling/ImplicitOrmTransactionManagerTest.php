<?php

namespace CQRSTest\Plugin\Doctrine\CommandHandling;

use CQRS\Plugin\Doctrine\CommandHandling\ImplicitOrmTransactionManager;
use CQRSTest\Plugin\Doctrine\Mock\EntityManagerMock;

class ImplicitOrmTransactionManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ImplicitOrmTransactionManager */
    protected $transactionManager;
    /** @var EntityManagerMock */
    protected $entityManager;

    public function setUp()
    {
        $this->entityManager = new EntityManagerMock();

        $this->transactionManager = new ImplicitOrmTransactionManager();
        $this->transactionManager->setEntityManager($this->entityManager);
    }

    public function testBeginTransaction()
    {
        $this->transactionManager->begin();

        $this->assertFalse($this->entityManager->begin);
    }

    public function testCommitTransaction()
    {
        $this->transactionManager->commit();

        $this->assertTrue($this->entityManager->flush);
        $this->assertFalse($this->entityManager->commit);
    }

    public function testRollbackTransaction()
    {
        $this->transactionManager->rollback();

        $this->assertFalse($this->entityManager->rollback);
        $this->assertFalse($this->entityManager->close);
    }
}

