<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\Command;
use CQRS\CommandHandling\CommandHandlerLocator;
use CQRS\CommandHandling\SequentialCommandBus;
use CQRS\CommandHandling\TransactionManager;

class SequentialCommandBusTest extends \PHPUnit_Framework_TestCase
{
    /** @var SequentialCommandBus */
    protected $commandBus;
    /** @var SequentialCommandHandler */
    protected $handler;
    /** @var SequentialTransactionManager */
    protected $transactionManager;

    public function setUp()
    {
        $this->handler = new SequentialCommandHandler();

        $locator = new SequentialCommandHandlerLocator();
        $locator->handler = $this->handler;

        $this->transactionManager = new SequentialTransactionManager();

        $this->commandBus = new SequentialCommandBus($locator, $this->transactionManager);
        $this->handler->commandBus = $this->commandBus;
    }

    public function testHandlingOfSequentialCommand()
    {
        $this->commandBus->handle(new DoSequentialCommand());

        $this->assertEquals(1, $this->handler->sequential);
        $this->assertEquals(1, $this->handler->simple);

        $this->assertEquals(1, $this->transactionManager->begin);
        $this->assertEquals(1, $this->transactionManager->commit);
        $this->assertEquals(0, $this->transactionManager->rollback);
    }

    public function testItRollbacksTransactionOnFailure()
    {
        $this->setExpectedException('CQRSTest\CommandHandling\CommandFailureTestException');

        try {
            $this->commandBus->handle(new DoFailureCommand());
        } catch (CommandFailureTestException $e) {

            $this->assertEquals(1, $this->transactionManager->begin);
            $this->assertEquals(0, $this->transactionManager->commit);
            $this->assertEquals(1, $this->transactionManager->rollback);

            throw $e;
        }
    }

    public function testItIgnoresErrorOnSequentialFailure()
    {
        $this->commandBus->handle(new DoSequentialFailureCommand());

        $this->assertEquals(1, $this->transactionManager->begin);
        $this->assertEquals(1, $this->transactionManager->commit);
        $this->assertEquals(0, $this->transactionManager->rollback);
    }

    public function testItThrowsExceptionWhenServiceHasNoHandlingMethod()
    {
        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'Service CQRSTest\CommandHandling\SequentialCommandHandler has no method noHandlingMethod to handle command'
        );

        $this->commandBus->handle(new NoHandlingMethodCommand());
    }
}

class DoSimpleCommand implements Command
{}

class DoSequentialCommand implements Command
{}

class DoFailureCommand implements Command
{}

class DoSequentialFailureCommand implements Command
{}

class NoHandlingMethodCommand implements Command
{}

class SequentialCommandHandlerLocator implements CommandHandlerLocator
{
    public $handler;

    public function getCommandHandler(Command $command)
    {
        return $this->handler;
    }
}

class SequentialCommandHandler
{
    /** @var SequentialCommandBus */
    public $commandBus;

    public $simple = 0;
    public $sequential = 0;

    public function doSimple(DoSimpleCommand $command)
    {
        $this->simple++;
    }

    public function doSequential(DoSequentialCommand $command)
    {
        $this->sequential++;
        $this->commandBus->handle(new DoSimpleCommand());
    }

    public function doFailure(DoFailureCommand $command)
    {
        throw new CommandFailureTestException();
    }

    public function doSequentialFailure(DoSequentialFailureCommand $command)
    {
        $this->commandBus->handle(new DoFailureCommand());
    }
}

class CommandFailureTestException extends \Exception
{}

class SequentialTransactionManager implements TransactionManager
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
