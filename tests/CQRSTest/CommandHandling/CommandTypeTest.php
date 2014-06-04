<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\Command;
use CQRS\CommandHandling\CommandType;

class CommandTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandTypeToString()
    {
        $command = new CommandTypeTestCommand();

        $commandType = new CommandType($command);

        $this->assertEquals('CQRSTest\CommandHandling\CommandTypeTestCommand', $commandType);
    }
}

class CommandTypeTestCommand implements Command
{}
