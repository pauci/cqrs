<?php

namespace CQRS\CommandHandling;

interface Command
{
    public function getCommandType();
}
