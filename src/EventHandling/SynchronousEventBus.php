<?php

declare(strict_types=1);

namespace CQRS\EventHandling;

use CQRS\Domain\Message\DomainEventMessageInterface;
use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericEventMessage;
use Psr\Container\ContainerInterface;

class SynchronousEventBus extends AbstractEventBus
{
    private ContainerInterface $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public function getLocator(): ContainerInterface
    {
        return $this->locator;
    }

    /**
     * @throws \Exception
     */
    public function publish(EventMessageInterface $event): void
    {
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
     * @throws \Exception
     */
    private function invokeEventHandler(callable $handler, EventMessageInterface $event): void
    {
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
}
