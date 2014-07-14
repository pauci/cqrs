<?php

namespace CQRS\CommandHandling;

class CommandType
{
    /** @var CommandInterface */
    private $command;

    /**
     * @param CommandInterface $command
     */
    public function __construct(CommandInterface $command)
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
