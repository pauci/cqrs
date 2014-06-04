<?php

namespace CQRS\CommandHandling;

class CommandType
{
    /** @var Command */
    private $command;

    /**
     * @param Command $command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this->command);
    }
} 
