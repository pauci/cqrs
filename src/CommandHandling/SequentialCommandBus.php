<?php

declare(strict_types=1);

namespace CQRS\CommandHandling;

use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use Exception;

/**
 * Process Commands and pass them to their handlers in sequential order.
 *
 * If commands are triggered within command handlers, this command bus puts
 * them on a stack and waits with the execution to allow sequential processing
 * and avoiding nested transactions.
 *
 * Any command handler execution can be wrapped by additional handlers to form
 * a chain of responsibility. To control this process you can pass an array of
 * proxy factories into the CommandBusInterface. The factories are iterated in REVERSE
 * order and get passed the current handler to stack the chain of
 * responsibility. That means the proxy factory registered FIRST is the one
 * that wraps itself around the previous handlers LAST.
 */
class SequentialCommandBus implements CommandBusInterface
{
    private CommandHandlerLocatorInterface $locator;

    private TransactionManagerInterface $transactionManager;

    private EventPublisherInterface $eventPublisher;

    private array $commandStack = [];

    private bool $executing = false;

    public function __construct(
        CommandHandlerLocatorInterface $locator,
        TransactionManagerInterface $transactionManager,
        EventPublisherInterface $eventPublisher
    ) {
        $this->locator = $locator;
        $this->transactionManager = $transactionManager;
        $this->eventPublisher = $eventPublisher;
    }

    /**
     * Sequentially execute commands
     *
     * If an exception occurs in any command it will be put on a stack
     * of exceptions that is thrown only when all the commands are processed.
     *
     * @throws Exception
     */
    public function dispatch(object $command): void
    {
        $this->commandStack[] = $command;

        if ($this->executing) {
            return;
        }

        $this->transactionManager->begin();

        try {
            while ($command = array_shift($this->commandStack)) {
                $this->invokeHandler($command);
            }

            $this->eventPublisher->publishEvents();
            $this->transactionManager->commit();
        } catch (Exception $e) {
            $this->transactionManager->rollback();

            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    protected function invokeHandler(object $command): void
    {
        try {
            $this->executing = true;

            $commandType = get_class($command);
            $handler = $this->locator->get($commandType);
            $handler($command);
        } catch (Exception $e) {
            $this->executing = false;

            throw $e;
        }

        $this->executing = false;
    }
}
