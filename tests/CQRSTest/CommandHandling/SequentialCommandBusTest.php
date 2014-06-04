<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\Command;
use CQRS\CommandHandling\CommandHandlerLocator;
use CQRS\CommandHandling\SequentialCommandBus;
use CQRS\CommandHandling\TransactionManager;

class SequentialCommandBusTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleSimpleCommand()
    {
        $locator = new TestCommandHandlerLocator();
        $locator->handler = new TestCommandHandler();

        $transactionManager = new TestTransactionManager();

        $command = new TestSimpleCommand();

        $commandBus = new SequentialCommandBus($locator, $transactionManager);
        $commandBus->handle($command);

        $this->assertEquals(1, $locator->handler->simpleCount);
        $this->assertSame($command, $locator->handler->command);

        $this->assertEquals(1, $transactionManager->begin);
        $this->assertEquals(1, $transactionManager->commit);
        $this->assertEquals(0, $transactionManager->rollback);
    }

    public function testHandleSequentialCommand()
    {
        $locator = new TestCommandHandlerLocator();
        $locator->handler = new TestCommandHandler();

        $transactionManager = new TestTransactionManager();

        $command = new TestSequentialCommand();

        $commandBus = new SequentialCommandBus($locator, $transactionManager);
        $locator->handler->commandBus = $commandBus;
        $commandBus->handle($command);

        $this->assertEquals(1, $locator->handler->simpleCount);
        $this->assertEquals(1, $locator->handler->sequentialCount);
        $this->assertNotSame($command, $locator->handler->command);

        $this->assertEquals(1, $transactionManager->begin);
        $this->assertEquals(1, $transactionManager->commit);
        $this->assertEquals(0, $transactionManager->rollback);
    }
}

class TestSimpleCommand implements Command
{}

class TestSequentialCommand implements Command
{}

class TestCommandHandlerLocator implements CommandHandlerLocator
{
    public $handler;

    public function getCommandHandler(Command $command)
    {
        return $this->handler;
    }
}

class TestCommandHandler
{
    public $simpleCount = 0;
    public $sequentialCount = 0;
    public $command;
    /** @var SequentialCommandBus */
    public $commandBus;

    public function testSimple(TestSimpleCommand $command)
    {
        $this->simpleCount++;
        $this->command = $command;
    }

    public function testSequential(TestSequentialCommand $command)
    {
        $this->sequentialCount++;
        $this->commandBus->handle(new TestSimpleCommand());
    }
}

class TestTransactionManager implements TransactionManager
{
    public $begin = 0;
    public $commit = 0;
    public $rollback = 0;

    public function begin()
    {
        $this->begin++;
    }

    public function commit()
    {
        $this->commit++;
    }

    public function rollback()
    {
        $this->rollback++;
    }
}
