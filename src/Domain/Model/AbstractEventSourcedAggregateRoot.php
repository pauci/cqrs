<?php

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\Metadata;
use CQRS\Exception\BadMethodCallException;

abstract class AbstractEventSourcedAggregateRoot extends AbstractAggregateRoot
{
    /**
     * @param object $payload
     * @param Metadata|array $metadata
     */
    protected function apply($payload, $metadata = null)
    {
        $event = $this->registerEvent($payload, $metadata);
        $this->handle($event);
    }

    /**
     * @param EventMessageInterface $event
     */
    private function handle(EventMessageInterface $event)
    {
        $eventName  = $this->getEventName($event);
        $methodName = 'apply' . $eventName;

        if (!method_exists($this, $methodName)) {
            throw new BadMethodCallException(sprintf(
                'Aggregate root %s has no method to apply event %s', get_class($this), $eventName
            ));
        }
        $this->$methodName(
            $event->getPayload(),
            $event->getTimestamp(),
            $event->getMetadata()
        );
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