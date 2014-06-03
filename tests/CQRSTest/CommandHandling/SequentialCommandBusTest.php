<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\Command;
use CQRS\CommandHandling\CommandHandlerLocator;
use CQRS\CommandHandling\SequentialCommandBus;
use CQRS\CommandHandling\TransactionManager;

class SequentialCommandBusTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandHandling()
    {
        $handler = new TestCommandHandler();

        $locator = new TestCommandHandlerLocator();
        $locator->handler = $handler;

        $transactionManager = new TestTransactionManager();

        $command = new TestSequentialCommand();

        $commandBus = new SequentialCommandBus($locator, $transactionManager);
        $commandBus->handle($command);

        $this->assertEquals(1, $handler->count);
        $this->assertSame($command, $handler->command);

        $this->assertEquals(1, $transactionManager->begin);
        $this->assertEquals(1, $transactionManager->commit);
        $this->assertEquals(0, $transactionManager->rollback);
    }
}

class TestSequentialCommand implements Command
{
    public function getCommandType()
    {
        return 'Test\TestMeCommand';
    }
}

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
    public $count = 0;
    public $command;

    public function testMe(TestSequentialCommand $command)
    {
        $this->count++;
        $this->command = $command;
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
