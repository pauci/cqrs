<?php

namespace CQRS\CommandHandling;

use CQRS\CommandHandling\Locator\CommandHandlerLocatorInterface;
use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use CQRS\Exception\RuntimeException;
use Exception;
use Psr\Log\LoggerInterface;

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
    /** @var CommandHandlerLocatorInterface */
    private $locator;

    /** @var TransactionManagerInterface */
    private $transactionManager;

    /** @var EventPublisherInterface */
    private $eventPublisher;

    /** @var CommandInterface[] */
    private $commandStack = [];

    /** @var bool */
    private $executing = false;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param CommandHandlerLocatorInterface $locator
     * @param TransactionManagerInterface $transactionManager
     * @param EventPublisherInterface $eventPublisher
     * @param LoggerInterface $logger
     */
    public function __construct(CommandHandlerLocatorInterface $locator, TransactionManagerInterface $transactionManager,
                                EventPublisherInterface $eventPublisher, LoggerInterface $logger)
    {
        $this->locator            = $locator;
        $this->transactionManager = $transactionManager;
        $this->eventPublisher     = $eventPublisher;
        $this->logger             = $logger;
    }

    /**
     * @return CommandHandlerLocatorInterface
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
     * @param CommandInterface $command
     * @throws Exception
     */
    public function handle(CommandInterface $command)
    {
        $this->logger->debug(sprintf("Handling Command %s",  get_class($command)), [
            'command' => $command
        ]);
        $this->commandStack[] = $command;

        if ($this->executing) {
            return;
        }

        $first = true;

        $this->transactionManager->begin();
        try {
            while ($command = array_shift($this->commandStack)) {
                $this->invokeHandler($command, $first);
                $first = false;
            }
            $this->eventPublisher->publishEvents();
            $this->transactionManager->commit();
        } catch (Exception $e) {
            $this->error("Exception ocured while handling command " . get_class($command));
            $this->transactionManager->rollback();
            throw $e;
        }
    }

    /**
     * @param CommandInterface $command
     * @param bool $first
     */
    protected function invokeHandler(CommandInterface $command, $first)
    {
        try {
            $this->executing = true;

            $service = $this->locator->getCommandHandler($command);
            $method  = $this->getHandlerMethodName($command);

            if (!method_exists($service, $method)) {
                throw new RuntimeException(sprintf(
                    'Service %s has no method %s to handle command',
                    get_class($service),
                    $method
                ));
            }

            $this->logger->debug(sprintf("Dispatching Command %s to CommandHandler %s",  get_class($command), get_class($service)), [
                'handlerMethod' => $method,
                'command'       => $command
            ]);

            $service->$method($command);

        } catch (Exception $e) {
            $this->executing = false;
            $this->handleException($e, $first);
        }

        $this->executing = false;
    }

    /**
     * @param CommandInterface $command
     * @return string
     */
    protected function getHandlerMethodName(CommandInterface $command)
    {
        $parts = explode('\\', get_class($command));

        return str_replace('Command', '', lcfirst(end($parts)));
    }

    /**
     * @param Exception $e
     * @param bool $first
     * @throws Exception
     */
    protected function handleException(Exception $e, $first)
    {
        if ($first) {
            throw $e;
        }
    }
}
