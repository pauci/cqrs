<?php

declare(strict_types=1);

namespace CQRS\CommandHandling;

use Psr\Container\ContainerInterface;

class CommandHandlerLocator implements ContainerInterface
{
    protected array $handlers = [];

    /**
     * @var callable|null
     */
    protected $resolver;

    /**
     * @param array $handlers
     * @param callable|null $resolver
     */
    public function __construct(array $handlers = [], callable $resolver = null)
    {
        foreach ($handlers as $commandType => $handler) {
            $this->set($commandType, $handler);
        }

        $this->resolver = $resolver;
    }

    /**
     * @param mixed $handler
     */
    public function set(string $commandType, $handler): void
    {
        $this->handlers[$commandType] = $handler;
    }

    public function remove(string $commandType): void
    {
        unset($this->handlers[$commandType]);
    }

    /**
     * @param mixed $handler
     */
    public function removeHandler($handler): void
    {
        foreach ($this->handlers as $commandType => $evaluatedHandler) {
            if ($handler === $evaluatedHandler) {
                unset($this->handlers[$commandType]);
            }
        }
    }

    /**
     * @param string $commandType
     * @return callable
     * @throws Exception\CommandHandlerNotFoundException
     */
    public function get($commandType): callable
    {
        if (!$this->has($commandType)) {
            throw new Exception\CommandHandlerNotFoundException(sprintf(
                'Command handler for %s not found',
                $commandType
            ));
        }

        return $this->resolver
            ? call_user_func($this->resolver, $this->handlers[$commandType], $commandType)
            : $this->handlers[$commandType];
    }

    /**
     * @param string $commandType
     */
    public function has($commandType): bool
    {
        return array_key_exists($commandType, $this->handlers);
    }
}
