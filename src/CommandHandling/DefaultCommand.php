<?php

namespace CQRS\CommandHandling;

use CQRS\Exception\RuntimeException;

/**
 * Default Implementation for the Command interface.
 *
 * Convenience Command that helps with construction by mapping an array input
 * to command properties. If a passed property does not exist on the class
 * an exception is thrown.
 *
 * @example
 *
 *   class GreetCommand extends DefaultCommand
 *   {
 *      public $personId;
 *   }
 *   $command = new GreetCommand(['personId' => 1]);
 *   $commandBus->handle($command);
 */
abstract class DefaultCommand implements Command
{
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->assertPropertyExists($key);
            $this->$key = $value;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $this->assertPropertyExists($name);
        return $this->$name;
    }

    /**
     * @param string $name
     * @throws RuntimeException
     */
    private function assertPropertyExists($name)
    {
        if (!property_exists($this, $name)) {
            $parts   = explode('\\', get_class($this));
            $command = end($parts);
            if (substr($command, -7) == 'Command') {
                $command = substr($command, 0, -7);
            }

            throw new RuntimeException(sprintf(
                'Property "%s" is not a valid property on command "%s"',
                $name,
                $command
            ));
        }
    }
}
