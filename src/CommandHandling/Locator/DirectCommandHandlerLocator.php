<?php

namespace CQRS\CommandHandling\Locator;

use CQRS\Exception\RuntimeException;
use Interop\Container\ContainerInterface;

class DirectCommandHandlerLocator implements ContainerInterface
{
    /**
     * @var array
     */
    private $handlers = [];

    /**
     * @param array $handlers
     */
    public function __construct(array $handlers = [])
    {
        foreach ($handlers as $commandType => $handler) {
            $this->add($commandType, $handler);
        }
    }

    /**
     * @param string $commandType
     * @param mixed $handler
     * @throws RuntimeException
     */
    public function add($commandType, $handler)
    {
        if (!is_callable($handler)) {
            throw new RuntimeException(sprintf(
                'No valid command handler given for %s; expected callable, got %s',
                $commandType,
                is_object($handler) ? get_class($handler) : gettype($handler)
            ));
        }

        $this->handlers[$commandType] = $handler;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws RuntimeException
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new CommandHandlerNotFoundException(sprintf('Command handler for %s not found', $id));
        }

        return $this->handlers[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->handlers[$id]);
    }
}
