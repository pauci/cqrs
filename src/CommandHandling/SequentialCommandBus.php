<?php

namespace CQRS\CommandHandling;

use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use CQRS\Exception\RuntimeException;
use Exception;
use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
    /**
     * @var ContainerInterface
     */
    private $locator;

    /**
     * @var TransactionManagerInterface
     */
    private $transactionManager;

    /**
     * @var EventPublisherInterface
     */
    private $eventPublisher;

    /**
     * @var array
     */
    private $commandStack = [];

    /**
     * @var bool
     */
    private $executing = false;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ContainerInterface $locator
     * @param TransactionManagerInterface $transactionManager
     * @param EventPublisherInterface $eventPublisher
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ContainerInterface $locator,
        TransactionManagerInterface $transactionManager,
        EventPublisherInterface $eventPublisher,
        LoggerInterface $logger = null
    ) {

        $this->locator = $locator;
        $this->transactionManager = $transactionManager;
        $this->eventPublisher = $eventPublisher;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @return ContainerInterface
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * @return TransactionManagerInterface
     */
    public function getTransactionManager()
    {
        return $this->transactionManager;
    }

    /**
     * @return EventPublisherInterface
     */
    public function getEventPublisher()
    {
        return $this->eventPublisher;
    }

    /**
     * Sequentially execute commands
     *
     * If an exception occurs in any command it will be put on a stack
     * of exceptions that is thrown only when all the commands are processed.
     *
     * @param mixed $command
     * @throws Exception
     */
    public function dispatch($command)
    {
        $this->logger->debug(sprintf(
            'Handling Command %s',
            get_class($command)
        ), [
            'command_payload_type' => get_class($command),
            'command_payload' => (array)$command,
        ]);

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
            $this->logger->error(sprintf(
                'Uncaught Exception %s while handling Command %s: "%s" at %s line %s',
                get_class($e),
                get_class($command),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ), [
                'exception' => $e,
                'command_payload_type' => get_class($command),
                'command_payload' => (array)$command,
            ]);

            $this->transactionManager->rollback();
            throw $e;
        }
    }

    /**
     * @param mixed $command
     * @throws Exception
     */
    protected function invokeHandler($command)
    {
        try {
            $this->executing = true;

            $commandType = get_class($command);
            $handler = $this->locator->get($commandType);

            if (!is_callable($handler)) {
                throw new RuntimeException(sprintf(
                    'Command handler %s is not invokable',
                    is_object($handler) ? get_class($handler) : gettype($handler)
                ));
            }

            $this->logger->debug('Invoking command handler', [
                'command_payload_type' => get_class($command),
                'command_payload' => (array) $command,
            ]);

            $handler($command);
        } catch (Exception $e) {
            $this->executing = false;
            throw $e;
        }

        $this->executing = false;
    }
}
