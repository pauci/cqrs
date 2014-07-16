<?php

namespace CQRS\Domain\Message;

use CQRS\CommandHandling\CommandInterface;
use CQRS\Exception\RuntimeException;

abstract class AbstractCommand extends AbstractMessage implements CommandInterface
{
    /**
     * @param string $name
     * @throws RuntimeException
     */
    protected function throwPropertyIsNotValidException($name)
    {
        throw new RuntimeException(sprintf(
            'Property "%s" is not a valid property on command "%s"',
            $name,
            get_class($this)
        ));
    }
}
