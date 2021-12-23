<?php

declare(strict_types=1);

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\SequentialCommandBus;
use PHPUnit\Framework\TestCase;

class SequentialCommandBusTest extends TestCase
{
    private SequentialCommandBus $commandBus;

    private Stubs\DummyCommandHandler $handler;

    private Stubs\DummyTransactionManager $transactionManager;

    private Stubs\DummyEventPublisher $eventPublisher;

    public function setUp(): void
    {
        $this->handler = new Stubs\DummyCommandHandler();

        $locator = new Stubs\DummyCommandHandlerLocator();
        $locator->handlers = [
            Stubs\DoSimpleCommand::class => [$this->handler, 'doSimple'],
            Stubs\DoSequentialCommand::class => [$this->handler, 'doSequential'],
            Stubs\DoFailureCommand::class => [$this->handler, 'doFailure'],
            Stubs\DoSequentialFailureCommand::class => [$this->handler, 'doSequentialFailure'],
        ];

        $this->transactionManager = new Stubs\DummyTransactionManager();

        $this->eventPublisher = new Stubs\DummyEventPublisher();

        $this->commandBus = new SequentialCommandBus($locator, $this->transactionManager, $this->eventPublisher);
        $this->handler->commandBus = $this->commandBus;
    }

    public function testHandlingOfSequentialCommand(): void
    {
        $this->commandBus->dispatch(new Stubs\DoSequentialCommand());

        self::assertEquals(1, $this->handler->sequential);
        self::assertEquals(1, $this->handler->simple);

        self::assertEquals(1, $this->transactionManager->begin);
        self::assertEquals(1, $this->transactionManager->commit);
        self::assertEquals(0, $this->transactionManager->rollback);

        self::assertEquals(1, $this->eventPublisher->published);
    }

    public function testItRollbacksTransactionOnFailure(): void
    {
        $this->expectException(Stubs\CommandFailureTestException::class);

        try {
            $this->commandBus->dispatch(new Stubs\DoFailureCommand());
        } catch (Stubs\CommandFailureTestException $e) {
            self::assertEquals(1, $this->transactionManager->begin);
            self::assertEquals(0, $this->transactionManager->commit);
            self::assertEquals(1, $this->transactionManager->rollback);

            self::assertEquals(0, $this->eventPublisher->published);

            throw $e;
        }
    }

    public function testItDoesntIgnoreErrorOnSequentialFailure(): void
    {
        $this->expectException(Stubs\CommandFailureTestException::class);

        $this->commandBus->dispatch(new Stubs\DoSequentialFailureCommand());

        self::assertEquals(1, $this->transactionManager->begin);
        self::assertEquals(1, $this->transactionManager->commit);
        self::assertEquals(0, $this->transactionManager->rollback);

        self::assertEquals(1, $this->eventPublisher->published);
    }
}
