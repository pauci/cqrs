<?php

declare(strict_types=1);

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\SequentialCommandBus;
use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use CQRS\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class SequentialCommandBusTest extends TestCase
{
    private SequentialCommandBus $commandBus;

    private SequentialCommandHandler $handler;

    private SequentialTransactionManager $transactionManager;

    private SequentialEventPublisher $eventPublisher;

    public function setUp(): void
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

    public function testHandlingOfSequentialCommand(): void
    {
        $this->commandBus->dispatch(new DoSequentialCommand());

        $this->assertEquals(1, $this->handler->sequential);
        $this->assertEquals(1, $this->handler->simple);

        $this->assertEquals(1, $this->transactionManager->begin);
        $this->assertEquals(1, $this->transactionManager->commit);
        $this->assertEquals(0, $this->transactionManager->rollback);

        $this->assertEquals(1, $this->eventPublisher->published);
    }

    public function testItRollbacksTransactionOnFailure(): void
    {
        $this->expectException(CommandFailureTestException::class);

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

    public function testItDoesntIgnoreErrorOnSequentialFailure(): void
    {
        $this->expectException(CommandFailureTestException::class);

        $this->commandBus->dispatch(new DoSequentialFailureCommand());

        $this->assertEquals(1, $this->transactionManager->begin);
        $this->assertEquals(1, $this->transactionManager->commit);
        $this->assertEquals(0, $this->transactionManager->rollback);

        $this->assertEquals(1, $this->eventPublisher->published);
    }

    public function testItThrowsExceptionWhenServiceHasNoHandlingMethod(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Command handler string is not invokable');

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
    public SequentialCommandBus $commandBus;

    public int $simple = 0;

    public int $sequential = 0;

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
    public int $begin = 0;

    public int $commit = 0;

    public int $rollback = 0;

    public function begin(): void
    {
        $this->begin++;
    }

    public function commit(): void
    {
        $this->commit++;
    }

    public function rollback(): void
    {
        $this->rollback++;
    }
}

class SequentialEventPublisher implements EventPublisherInterface
{
    public int $published = 0;

    public function publishEvents(): void
    {
        $this->published++;
    }
}
