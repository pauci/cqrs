<?php

namespace CQRS\HandlerResolver;

use Interop\Container\ContainerInterface;

class ContainerHandlerResolver
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var callable
     */
    protected $nextResolver;

    /**
     * @param ContainerInterface $container
     * @param callable|null $nextResolver
     */
    public function __construct(ContainerInterface $container, callable $nextResolver = null)
    {
        $this->container = $container;
        $this->nextResolver = $nextResolver;
    }

    /**
     * @param mixed $handler
     * @param string $messageType
     * @return callable
     */
    public function __invoke($handler, $messageType)
    {
        if (is_string($handler)) {
            $handler = $this->container->get($handler);
        } elseif (is_array($handler) && array_key_exists(0, $handler) && is_string($handler[0])) {
            $handler[0] = $this->container->get($handler[0]);
        }

        if ($this->nextResolver) {
            $handler = call_user_func($this->nextResolver, $handler, $messageType);
        }

        return $handler;
    }
}
