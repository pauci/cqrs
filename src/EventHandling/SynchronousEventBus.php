<?php

namespace CQRS\EventHandling;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericEventMessage;
use CQRS\EventHandling\Locator\EventHandlerLocatorInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class SynchronousEventBus implements EventBusInterface
{
    use LoggerTrait;

    /** @var EventHandlerLocatorInterface */
    private $locator;

    /** @var LoggerInterface */
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
        $this->info('Invoke event handler', [
            'payload'        => get_class($event->getPayload()),
            'aggregateType'  => $event->getAggregateType(),
            'aggregateId'    => $event->getAggregateId(),
            'sequenceNumber' => $event->getSequenceNumber(),
            'listener'       => is_array($callback) ? get_class($callback[0]) . "::" . $callback[1] : 'closure',
            'payload'        => $event->getPayload(),
            'metadata'       => $event->getMetadata()
        ]);

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

    /**
     * Logs with an arbitrary level.
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->logger->log($level, $message, $context);
    }
}
