<?php

namespace CQRS\EventHandling;

use Interop\Container\ContainerInterface;

class EventHandlerLocator implements ContainerInterface
{
    /**
     * @var array
     */
    protected $handlers = [];

    /**
     * @var callable
     */
    protected $resolver;

    /**
     * @param array $handlers
     * @param callable $resolver
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(array $handlers = [], callable $resolver = null)
    {
        foreach ($handlers as $eventType => $eventHandlers) {
            if (!is_array($eventHandlers)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Handlers for event %s must be specified as array; got %s',
                    $eventType,
                    is_object($eventHandlers) ? get_class($eventHandlers) : gettype($eventHandlers)
                ));
            }

            $priority = 1;
            foreach ($eventHandlers as $handler) {
                if (is_array($handler) && array_key_exists('handler', $handler)) {
                    if (array_key_exists('priority', $handler)) {
                        $priority = $handler['priority'];
                    }
                    $handler = $handler['handler'];
                }
                $this->add($eventType, $handler, $priority);
            }
        }

        $this->resolver = $resolver;
    }

    /**
     * @param string $eventType
     * @param mixed $handler
     * @param int $priority
     * @throws Exception\InvalidArgumentException
     */
    public function add($eventType, $handler, $priority = 1)
    {
        if (!is_string($eventType)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Event type must be a string; got %s',
                is_object($eventType) ? get_class($eventType) : gettype($eventType)
            ));
        }

        if (array_key_exists($eventType, $this->handlers)
            && array_key_exists($priority, $this->handlers[$eventType])
            && in_array($handler, $this->handlers[$eventType], true)
        ) {
            return;
        }

        // Make sure the priority index is string so we can use array_merge_recursive
        $this->handlers[$eventType][(int) $priority . '.0'][] = $handler;
    }

    /**
     * @param mixed $handler
     * @param string|null $eventType
     * @throws Exception\InvalidArgumentException
     */
    public function remove($handler, $eventType = null)
    {
        // If event type is not specified, we need to iterate through each event type
        if (null === $eventType) {
            foreach ($this->handlers as $eventType => $unused) {
                $this->remove($handler, $eventType);
            }
            return;
        }

        if (!is_string($eventType)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Event type must be a string; got %s',
                is_object($eventType) ? get_class($eventType) : gettype($eventType)
            ));
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
     * @param string $eventType
     * @return callable[]
     */
    public function get($eventType)
    {
        $handlers = array_merge_recursive(
            array_key_exists($eventType, $this->handlers) ? $this->handlers[$eventType] : [],
            array_key_exists('*', $this->handlers) ? $this->handlers['*'] : []
        );


        krsort($handlers, SORT_NUMERIC);

        $eventHandlers = [];
        foreach ($handlers as $priority => $handlersByPriority) {
            foreach ($handlersByPriority as $handler) {
                if ($this->resolver) {
                    $handler = call_user_func($this->resolver, $handler, $eventType);
                }

                $eventHandlers[] = $handler;
            }
        }

        return $eventHandlers;
    }

    /**
     * {@inheritdoc}
     */
    public function has($eventType)
    {
        return array_key_exists($eventType, $this->handlers) || array_key_exists('*', $this->handlers);
    }
}
