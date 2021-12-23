<?php

declare(strict_types=1);

namespace CQRS\CommandHandling;

use Psr\Container\ContainerInterface;

class PsrContainerCommandHandlerLocator implements CommandHandlerLocatorInterface
{
    protected ContainerInterface $container;

    protected array $handlers;

    /**
     * @param array<class-string, string> $handlers
     */
    public function __construct(ContainerInterface $container, array $handlers)
    {
        $this->container = $container;
        $this->handlers = $handlers;
    }

    /**
     * @return callable
     * @throws Exception\CommandHandlerNotFoundException
     */
    public function get(string $commandType): callable
    {
        if (!array_key_exists($commandType, $this->handlers)) {
            throw new Exception\CommandHandlerNotFoundException(sprintf(
                'Command handler for %s not found',
                $commandType
            ));
        }

        $handlerId = $this->handlers[$commandType];
        $handler = $this->container->get($handlerId);

        if (!is_callable($handler)) {
            throw new Exception\CommandHandlerNotFoundException(sprintf(
                'Command handler "%s" of type "%s" for command "%s" is not callable',
                $handlerId,
                get_debug_type($handler),
                $commandType
            ));
        }

        return $handler;
    }
}
