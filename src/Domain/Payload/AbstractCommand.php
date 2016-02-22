<?php

namespace CQRS\Domain\Payload;

use CQRS\Exception\RuntimeException;

/**
 * Default Implementation for the CommandInterface interface.
 *
 * Convenience CommandInterface that helps with construction by mapping an array input
 * to command properties. If a passed property does not exist on the class
 * an exception is thrown.
 *
 * @example
 *
 *   class GreetCommand extends AbstractCommand
 *   {
 *      public $personId;
 *   }
 *   $command = new GreetCommand(['personId' => 1]);
 *   $commandBus->handle($command);
 */
abstract class AbstractCommand extends AbstractPayload
{
    /**
     * @param string $name
     * @throws RuntimeException
     */
    protected function throwPropertyIsNotValidException($name)
    {
        $parts   = explode('\\', get_class($this));
        $command = end($parts);
        if (substr($command, -7) === 'Command') {
            $command = substr($command, 0, -7);
        }

        throw new RuntimeException(sprintf(
            'Property "%s" is not a valid property on command "%s"',
            $name,
            $command
        ));
    }
}
