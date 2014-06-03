<?php

namespace CQRSTest\CommandHandling;

use CQRS\CommandHandling\AbstractCommand;

class AbstractCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommandTypeReturnsFQCN()
    {
        $command = new TestAbstractCommand();
        $this->assertEquals('CQRSTest\CommandHandling\TestAbstractCommand', $command->getCommandType());
    }
}

class TestAbstractCommand extends AbstractCommand
{}
