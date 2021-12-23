<?php

declare(strict_types=1);

namespace CQRS\EventHandling;

use Psr\Container\ContainerInterface;

class PsrContainerEventHandlerLocator implements EventHandlerLocatorInterface
{
    protected ContainerInterface $container;

    protected array $handlers = [];

    /**
     * @param array<class-string, array<string|array{handler: string, priority?: int}>> $handlers
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(ContainerInterface $container, array $handlers = [])
    {
        foreach ($handlers as $eventType => $eventHandlers) {
            if (!is_array($eventHandlers)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Handlers for event %s must be specified as array; got %s',
                    $eventType,
                    get_debug_type($eventHandlers)
                ));
            }

            foreach ($eventHandlers as $handler) {
                $priority = 1;

                if (is_array($handler) && array_key_exists('handler', $handler)) {
                    if (array_key_exists('priority', $handler)) {
                        $priority = $handler['priority'];
                    }

                    $handler = $handler['handler'];
                }

                $this->add($eventType, $handler, $priority);
            }
        }

        $this->container = $container;
    }

    /**
     * @param mixed $handler
     * @throws Exception\InvalidArgumentException
     */
    public function add(string $eventType, mixed $handler, int $priority = 1): void
    {
        if (
            array_key_exists($eventType, $this->handlers)
            && array_key_exists($priority, $this->handlers[$eventType])
            && in_array($handler, $this->handlers[$eventType], true)
        ) {
            return;
        }

        // Make sure the priority index is string so we can use array_merge_recursive
        $this->handlers[$eventType][$priority . '.0'][] = $handler;
    }

    /**
     * @param mixed $handler
     * @throws Exception\InvalidArgumentException
     */
    public function remove(mixed $handler, string $eventType = null): void
    {
        // If event type is not specified, we need to iterate through each event type
        if (null === $eventType) {
            foreach ($this->handlers as $eventType => $unused) {
                $this->remove($handler, $eventType);
            }

            return;
        }

        if (!array_key_exists($eventType, $this->handlers)) {
            return;
        }

        foreach ($this->handlers[$eventType] as $priority => $handlers) {
            foreach ($handlers as $index => $evaluatedHandler) {
                if ($evaluatedHandler !== $handler) {
                    continue;
                }

                // Found the handler; remove it.
                unset($this->handlers[$eventType][$priority][$index]);
            }

            // If the queue for the given priority is empty, remove it.
            if (empty($this->handlers[$eventType][$priority])) {
                unset($this->handlers[$eventType][$priority]);

                break;
            }
        }

        // If the queue for the given event is empty, remove it.
        if (empty($this->handlers[$eventType])) {
            unset($this->handlers[$eventType]);
        }
    }

    /**
     * Returns an array of event handlers sorted by priority from highest to lowest
     *
     * @return callable[]
     */
    public function get(string $eventType): array
    {
        $handlers = array_merge_recursive(
            $this->handlers[$eventType] ?? [],
            $this->handlers['*'] ?? [],
        );

        krsort($handlers, SORT_NUMERIC);

        $eventHandlers = [];
        foreach ($handlers as $priority => $handlersByPriority) {
            foreach ($handlersByPriority as $handlerId) {
                $handler = $this->container->get($handlerId);

                if (!is_callable($handler)) {
                    throw new Exception\RuntimeException(sprintf(
                        'Event handler "%s" of type "%s" for event "%s" is not callable',
                        $handlerId,
                        get_debug_type($handler),
                        $eventType
                    ));
                }

                $eventHandlers[] = $handler;
            }
        }

        return $eventHandlers;
    }
}
