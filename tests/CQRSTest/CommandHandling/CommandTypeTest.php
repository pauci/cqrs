<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\CommandInterface;
use CQRS\CommandHandling\CommandType;

class CommandTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandTypeToString()
    {
        $commandType = new CommandType(new CommandOfSomeType());

        $this->assertEquals('CQRSTest\CommandHandling\CommandOfSomeType', $commandType);
    }
}

class CommandOfSomeType implements CommandInterface
{}
