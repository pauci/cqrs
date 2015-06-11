<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\Locator\CommandHandlerLocatorInterface;
use CQRS\CommandHandling\SequentialCommandBus;
use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use PHPUnit_Framework_TestCase;
use Psr\Log\NullLogger;

class SequentialCommandBusTest extends PHPUnit_Framework_TestCase
{
    /** @var SequentialCommandBus */
    protected $commandBus;
    /** @var SequentialCommandHandler */
    protected $handler;
    /** @var SequentialTransactionManager */
    protected $transactionManager;
    /** @var SequentialEventPublisher */
    protected $eventPublisher;

    public function setUp()
    {
        $this->handler = new SequentialCommandHandler();

        $locator = new SequentialCommandHandlerLocator();
        $locator->handler = $this->handler;

        $this->transactionManager = new SequentialTransactionManager();

        $this->eventPublisher = new SequentialEventPublisher();

        $this->commandBus = new SequentialCommandBus($locator, $this->transactionManager, $this->eventPublisher, new NullLogger());
        $this->handler->commandBus = $this->commandBus;
    }

    public function testHandlingOfSequentialCommand()
    {
        $this->commandBus->dispatch(new DoSequentialCommand());

        $this->assertEquals(1, $this->handler->sequential);
        $this->assertEquals(1, $this->handler->simple);

        $this->assertEquals(1, $this->transactionManager->begin);
        $this->assertEquals(1, $this->transactionManager->commit);
        $this->assertEquals(0, $this->transactionManager->rollback);

        $this->assertEquals(1, $this->eventPublisher->published);
    }

    public function testItRollbacksTransactionOnFailure()
    {
        $this->setExpectedException('CQRSTest\CommandHandling\CommandFailureTestException');

        try {
            $this->commandBus->dispatch(new DoFailureCommand());
        } catch (CommandFailureTestException $e) {

            $this->assertEquals(1, $this->transactionManager->begin);
            $this->assertEquals(0, $this->transactionManager->commit);
            $this->assertEquals(1, $this->transactionManager->rollback);

            $this->assertEquals(0, $this->eventPublisher->published);

            throw $e;
        }
    }

    public function testItDoesntIgnoreErrorOnSequentialFailure()
    {
        $this->setExpectedException('CQRSTest\CommandHandling\CommandFailureTestException');

        $this->commandBus->dispatch(new DoSequentialFailureCommand());

        $this->assertEquals(1, $this->transactionManager->begin);
        $this->assertEquals(1, $this->transactionManager->commit);
        $this->assertEquals(0, $this->transactionManager->rollback);

        $this->assertEquals(1, $this->eventPublisher->published);
    }

    public function testItThrowsExceptionWhenServiceHasNoHandlingMethod()
    {
        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'Service CQRSTest\CommandHandling\SequentialCommandHandler has no method to handle Command CQRSTest\CommandHandling\NoHandlingMethodCommand'
        );

        $this->commandBus->dispatch(new NoHandlingMethodCommand());
    }
}

class DoSimpleCommand
{}

class DoSequentialCommand
{}

class DoFailureCommand
{}

class DoSequentialFailureCommand
{}

class NoHandlingMethodCommand
{}

class SequentialCommandHandlerLocator implements CommandHandlerLocatorInterface
{
    public $handler;

    public function getCommandHandler($command)
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
        $this->commandBus->dispatch(new DoSimpleCommand());
    }

    public function doFailure(DoFailureCommand $command)
    {
        throw new CommandFailureTestException();
    }

    public function doSequentialFailure(DoSequentialFailureCommand $command)
    {
        $this->commandBus->dispatch(new DoFailureCommand());
    }
}

class CommandFailureTestException extends \Exception
{}

class SequentialTransactionManager implements TransactionManagerInterface
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

class SequentialEventPublisher implements EventPublisherInterface
{
    public $published = 0;

    public function publishEvents()
    {
        $this->published++;
    }
}
