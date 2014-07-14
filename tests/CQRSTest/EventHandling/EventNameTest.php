<?php

namespace CQRSTest\EventHandling;

use CQRS\Domain\Message\EventMessageInterface;
use CQRS\EventHandling\EventName;

class EventNameTest extends \PHPUnit_Framework_TestCase
{
    public function testEventNameToString()
    {
        $event = new EventOfSomeName();
        $eventName = new EventName($event);

        $this->assertEquals('EventOfSomeName', (string) $eventName);
    }
}

class EventOfSomeName implements EventMessageInterface
{
    public function getTimestamp()
    {}

    public function getId()
    {}

    public function getMetadata()
    {}

    public function getPayload()
    {}
}
