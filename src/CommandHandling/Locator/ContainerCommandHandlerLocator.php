<?php

namespace CQRS\CommandHandling\Locator;

use Interop\Container\ContainerInterface;

class ContainerCommandHandlerLocator implements ContainerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $map;

    /**
     * @param ContainerInterface $container
     * @param array $map
     */
    public function __construct(ContainerInterface $container, array $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    /**
     * @param string $id
     * @throws CommandHandlerNotFoundException
     * @return mixed
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new CommandHandlerNotFoundException(sprintf('Command handler for %s not found', $id));
        }

        $handlerId = $this->map[$id];

        if (is_array($handlerId)) {
            $service = $this->container->get($handlerId[0]);
            return [$service, $handlerId[1]];
        }

        $handler = $this->container->get($handlerId);

        if (is_object($handler) && !is_callable($handler)) {
            $method = $this->resolveMethod($id);
            if (method_exists($handler, $method)) {
                return [$handler, $method];
            }
        }

        return $handler;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->map);
    }

    /**
     * @param string $id
     * @return string
     */
    private function resolveMethod($id)
    {
        // Remove namespace
        $pos = strrpos($id, '\\');
        if ($pos > 0) {
            $id = substr($id, $pos + 1);
        }

        // Remove "Command" suffix
        if (substr($id, -7) === 'Command') {
            $id = substr($id, 0, -7);
        }

        return lcfirst($id);
    }
}
