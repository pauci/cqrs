<?php

namespace CQRS\CommandHandling;

use Interop\Container\ContainerInterface;

class CommandHandlerLocator implements ContainerInterface
{
    /**
     * @var array
     */
    protected $handlers = [];

    /**
     * @var callable|null
     */
    protected $resolver;

    /**
     * @param array $handlers
     * @param callable $resolver
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(array $handlers = [], callable $resolver = null)
    {
        foreach ($handlers as $commandType => $handler) {
            $this->set($commandType, $handler);
        }

        $this->resolver = $resolver;
    }

    /**
     * @param string $commandType
     * @param mixed $handler
     * @throws Exception\InvalidArgumentException
     */
    public function set($commandType, $handler)
    {
        if (!is_string($commandType)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Command type must be a string; got %s',
                is_object($commandType) ? get_class($commandType) : gettype($commandType)
            ));
        }

        $this->handlers[$commandType] = $handler;
    }

    /**
     * @param string $commandType
     * @throws Exception\InvalidArgumentException
     */
    public function remove($commandType)
    {
        if (!is_string($commandType)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Command type must be a string; got %s',
                is_object($commandType) ? get_class($commandType) : gettype($commandType)
            ));
        }

        unset($this->handlers[$commandType]);
    }

    /**
     * @param mixed $handler
     */
    public function removeHandler($handler)
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
    public function get($commandType)
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
     * @return bool
     */
    public function has($commandType)
    {
        return array_key_exists($commandType, $this->handlers);
    }
}
