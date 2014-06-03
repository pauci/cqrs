<?php

namespace CQRS\CommandHandling;

abstract class AbstractCommand implements Command
{
    /**
     * @return string
     */
    public function getCommandType()
    {
        return get_class($this);
    }
}
