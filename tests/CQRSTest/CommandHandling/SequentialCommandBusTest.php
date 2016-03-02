<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\SequentialCommandBus;
use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;

class SequentialCommandBusTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SequentialCommandBus
     */
    private $commandBus;

    /**
     * @var SequentialCommandHandler
     */
    private $handler;

    /**
     * @var SequentialTransactionManager
     */
    private $transactionManager;

    /**
     * @var SequentialEventPublisher
     */
    private $eventPublisher;

    public function setUp()
    {
        $this->handler = new SequentialCommandHandler();

        $locator = new SequentialCommandHandlerLocator();
        $locator->handlers = [
            DoSimpleCommand::class => [$this->handler, 'doSimple'],
            DoSequentialCommand::class => [$this->handler, 'doSequential'],
            DoFailureCommand::class => [$this->handler, 'doFailure'],
            DoSequentialFailureCommand::class => [$this->handler, 'doSequentialFailure'],
            NotInvokableCommand::class => 'notInvokable',
        ];

        $this->transactionManager = new SequentialTransactionManager();

        $this->eventPublisher = new SequentialEventPublisher();

        $this->commandBus = new SequentialCommandBus($locator, $this->transactionManager, $this->eventPublisher);
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
            'Command handler string is not invokable'
        );

        $this->commandBus->dispatch(new NotInvokableCommand());
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

class NotInvokableCommand
{}

class SequentialCommandHandlerLocator implements ContainerInterface
{
    public $handlers;

    public function get($commandType)
    {
        return $this->handlers[$commandType];
    }

    public function has($commandType)
    {
        return true;
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
