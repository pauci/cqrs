<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\AbstractEventMessage;

class AbstractEventMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTimestamp()
    {
        $event = new AbstractEventMessageUnderTest();

        $this->assertInstanceOf('DateTimeImmutable', $event->getTimestamp());
    }
}

class AbstractEventMessageUnderTest extends AbstractEventMessage
{}
