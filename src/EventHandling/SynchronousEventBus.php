<?php

namespace CQRS\EventHandling;

use Closure;
use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericEventMessage;
use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class SynchronousEventBus extends AbstractEventBus
{
    /**
     * @var ContainerInterface
     */
    private $locator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ContainerInterface $locator
     * @param LoggerInterface|null $logger
     */
    public function __construct(ContainerInterface $locator, LoggerInterface $logger = null)
    {
        $this->locator = $locator;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @return ContainerInterface
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * @param EventMessageInterface $event
     * @throws Exception\RuntimeException
     */
    public function publish(EventMessageInterface $event)
    {
        $this->logger->debug(sprintf(
            'Publishing Event %s',
            $event->getPayloadType()
        ), [
            'event_id'           => $event->getId(),
            'event_payload_type' => $event->getPayloadType(),
            'event_payload'      => (array) $event->getPayload(),
            'event_metadata'     => $event->getMetadata()->toArray(),
            'event_timestamp'    => $event->getTimestamp()
        ]);

        $eventType = $event->getPayloadType();
        $eventHandlers = $this->locator->get($eventType);
        if (!is_array($eventHandlers)) {
            $eventHandlers = [$eventHandlers];
        }

        foreach ($eventHandlers as $handler) {
            if (!is_callable($handler)) {
                throw new Exception\RuntimeException(sprintf(
                    'Event handler %s is not invokable',
                    is_object($handler) ? get_class($handler) : gettype($handler)
                ));
            }

            $this->invokeEventHandler($handler, $event);
        }
    }

    /**
     * @param Callable $handler
     * @param EventMessageInterface $event
     * @throws \Exception
     */
    private function invokeEventHandler(callable $handler, EventMessageInterface $event)
    {
        $handlerName = $this->getHandlerName($handler);

        $this->logger->debug(sprintf(
            'Invoking event handler %s',
            $handlerName
        ), [
            'event_id' => $event->getId(),
            'event_type' => $event->getPayloadType(),
        ]);

        try {
            if ($event instanceof DomainEventMessageInterface) {
                $handler(
                    $event->getPayload(),
                    $event->getMetadata(),
                    $event->getTimestamp(),
                    $event->getSequenceNumber(),
                    $event->getAggregateId()
                );
            } else {
                $handler(
                    $event->getPayload(),
                    $event->getMetadata(),
                    $event->getTimestamp()
                );
            }
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                'Uncaught Exception %s while handling Event %s: "%s" at %s line %s',
                get_class($e),
                $event->getPayloadType(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ), [
                'exception' => $e,
                'event_id'  => $event->getId(),
            ]);

            if ($event->getPayload() instanceof EventExecutionFailed) {
                return;
            }

            $this->publish(new GenericEventMessage(
                new EventExecutionFailed($event, $e),
                $event->getMetadata()
            ));

            throw $e;
        }
    }

    /**
     * @param callable $handler
     * @return string
     */
    private function getHandlerName(callable $handler)
    {
        if (is_object($handler)) {
            return get_class($handler);
        }

        if (is_array($handler)) {
            list($object, $method) = $handler;
            return sprintf('%s::%s', is_object($object) ? get_class($object) : $object, $method);
        }

        if (is_string($handler)) {
            return $handler;
        }

        return Closure::class;
    }
}
