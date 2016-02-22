<?php

namespace CQRS\Domain\Payload;

use CQRS\Exception\RuntimeException;

/**
 * Default Implementation for the EventInterface interface.
 *
 * Convenience EventInterface that helps with construction by mapping an array input
 * to event properties. If a passed property does not exist on the class an exception is thrown.
 *
 * @example
 *
 *   class GreetedEvent extends AbstractEvent
 *   {
 *      public $personId;
 *   }
 *   $event = new GreetedEvent(['personId' => 1]);
 *   $eventBus->handle($event);
 */
abstract class AbstractEvent extends AbstractPayload
{
    /**
     * @param string $name
     * @throws RuntimeException
     */
    protected function throwPropertyIsNotValidException($name)
    {
        $parts = explode('\\', get_class($this));
        $event = end($parts);
        if (substr($event, -5) === 'Event') {
            $event = substr($event, 0, -5);
        }

        throw new RuntimeException(sprintf(
            'Property "%s" is not a valid property on event "%s"',
            $name,
            $event
        ));
    }
}
