<?php

namespace CQRSTest\Plugin\Doctrine\CommandHandling;

use CQRS\Plugin\Doctrine\CommandHandling\ExplicitOrmTransactionManager;
use CQRSTest\Plugin\Doctrine\Mock\EntityManagerMock;

class ExplicitOrmTransactionManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ExplicitOrmTransactionManager */
    protected $transactionManager;
    /** @var EntityManagerMock */
    protected $entityManager;

    public function setUp()
    {
        $this->entityManager = new EntityManagerMock();

        $this->transactionManager = new ExplicitOrmTransactionManager();
        $this->transactionManager->setEntityManager($this->entityManager);
    }

    public function testBeginTransaction()
    {
        $this->transactionManager->begin();

        $this->assertTrue($this->entityManager->begin);
    }

    public function testCommitTransaction()
    {
        $this->transactionManager->commit();

        $this->assertTrue($this->entityManager->flush);
        $this->assertTrue($this->entityManager->commit);
    }

    public function testRollbackTransaction()
    {
        $this->transactionManager->rollback();

        $this->assertTrue($this->entityManager->rollback);
        $this->assertTrue($this->entityManager->close);
    }
}

