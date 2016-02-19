<?php

namespace CQRS\Domain\Model;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\Exception\BadMethodCallException;
use CQRS\Exception\DomainException;

abstract class AbstractEventSourcedAggregateRoot extends AbstractAggregateRoot
{
    /**
     * @param mixed $payload
     * @param Metadata|array $metadata
     * @throws DomainException
     */
    protected function apply($payload, $metadata = null)
    {
        if ($this->getId() === null) {
            if ($this->getUncommittedEventsCount() > 0 || $this->getVersion() !== null) {
                throw new DomainException(
                    'The Aggregate ID has not been initialized. '
                    . 'It must be initialized at the latest when the first event is applied.'
                );
            }

            $message = new GenericDomainEventMessage(
                get_class($this),
                null,
                0,
                $payload,
                $metadata
            );

            $this->handle($message);
            $this->registerEventMessage($message);
        } else {
            $message = $this->registerEvent($payload, $metadata);
            $this->handle($message);
        }
    }

    /**
     * @param EventMessageInterface $eventMessage
     * @throws BadMethodCallException
     */
    private function handle(EventMessageInterface $eventMessage)
    {
        $eventName  = $this->getEventName($eventMessage);
        $methodName = 'apply' . $eventName;

        if (!method_exists($this, $methodName)) {
            throw new BadMethodCallException(sprintf(
                'Aggregate root %s has no method to apply event %s',
                get_class($this),
                $eventName
            ));
        }
        $this->$methodName(
            $eventMessage->getPayload(),
            $eventMessage->getTimestamp(),
            $eventMessage->getMetadata()
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

        if (substr($name, -5) === 'Event') {
            $name = substr($name, 0, -5);
        }

        return $name;
    }
}
