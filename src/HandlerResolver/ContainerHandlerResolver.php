<?php

declare(strict_types=1);

namespace CQRS\HandlerResolver;

use Closure;
use Psr\Container\ContainerInterface;

class ContainerHandlerResolver
{
    protected ContainerInterface $container;

    /**
     * @var callable|null
     */
    protected $nextResolver;

    public function __construct(ContainerInterface $container, callable $nextResolver = null)
    {
        $this->container = $container;
        $this->nextResolver = $nextResolver;
    }

    /**
     * @param mixed $handler
     */
    public function __invoke($handler, string $messageType): callable
    {
        if (is_string($handler)) {
            $handler = $this->container->get($handler);
        } elseif (is_array($handler) && array_key_exists(0, $handler) && is_string($handler[0])) {
            $handler[0] = $this->container->get($handler[0]);
        }

        if ($this->nextResolver) {
            $handler = ($this->nextResolver)($handler, $messageType);
        }

        return $handler;
    }
}
