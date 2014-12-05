<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericEventMessage;
use CQRS\EventHandling\Locator\EventHandlerLocatorInterface;
use Exception;
use Psr\Log\LoggerInterface;

class SynchronousEventBus implements EventBusInterface
{
    /**
     * @var EventHandlerLocatorInterface
     */
    private $locator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EventHandlerLocatorInterface $locator
     * @param LoggerInterface $logger
     */
    public function __construct(EventHandlerLocatorInterface $locator, LoggerInterface $logger)
    {
        $this->locator = $locator;
        $this->logger  = $logger;
    }

    /**
     * @return EventHandlerLocatorInterface
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * @param EventMessageInterface $event
     */
    public function publish(EventMessageInterface $event)
    {
        $this->logger->debug(sprintf(
            'Publishing event `%s`',
            $event->getPayloadType()
        ), [
            'event_id'        => $event->getId(),
            'event_payload'   => $event->getPayload(),
            'event_metadata'  => $event->getMetadata(),
            'event_timestamp' => $event->getTimestamp()
        ]);

        $eventName = $this->getEventName($event);
        $callbacks = $this->locator->getEventHandlers($eventName);

        foreach ($callbacks as $callback) {
            $this->invokeEventHandler($callback, $event);
        }
    }

    /**
     * @param Callable $callback
     * @param EventMessageInterface $event
     */
    private function invokeEventHandler(callable $callback, $event)
    {
        $handlerName = is_array($callback)
            ? (is_string($callback[0]) ? $callback[0] : get_class($callback[0])) . '::' . $callback[1]
            : 'closure';

        $this->logger->debug(sprintf(
            'Invoking event listener `%s` for event `%s`',
            $handlerName,
            $event->getPayloadType()
        ));

        try {
            if ($event instanceof DomainEventMessageInterface) {
                $callback(
                    $event->getPayload(),
                    $event->getMetadata(),
                    $event->getTimestamp(),
                    $event->getSequenceNumber(),
                    $event->getAggregateId()
                );
            } else {
                $callback(
                    $event->getPayload(),
                    $event->getMetadata(),
                    $event->getTimestamp()
                );
            }
        } catch (Exception $e) {
            $this->logger->error(sprintf(
                'Exception `%s` caught while handling event `%s` by event handler `%s`.',
                get_class($e),
                $event->getPayloadType(),
                $handlerName
            ));

            if ($event->getPayload() instanceof EventExecutionFailed) {
                return;
            }

            $this->publish(new GenericEventMessage(
                new EventExecutionFailed([
                    'exception' => $e,
                    'event'     => $event,
                ]),
                $event->getMetadata()->toArray()
            ));
        }
    }

    /**
     * @param EventMessageInterface $event
     * @return string
     */
    private function getEventName(EventMessageInterface $event)
    {
        $name = $event->getPayloadType();

        $pos = strrpos($name, '\\');
        if ($pos !== false) {
            $name = substr($name, $pos + 1);
        }

        if (substr($name, -5) == 'Event') {
            $name = substr($name, 0, -5);
        }

        return $name;
    }
}
