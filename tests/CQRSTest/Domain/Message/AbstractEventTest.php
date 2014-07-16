<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\AbstractEvent;
use CQRS\Exception\RuntimeException;
use DateTimeImmutable;
use Rhumsaa\Uuid\Uuid;

class AbstractEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUuid()
    {
        $event = new AbstractEventUnderTest();

        $this->assertInstanceOf(Uuid::class, $event->getId());
    }

    public function testGetTimestamp()
    {
        $event = new AbstractEventUnderTest();

        $this->assertInstanceOf(DateTimeImmutable::class, $event->getTimestamp());
    }

    public function testGetEventName()
    {
        $event = new AbstractEventUnderTest();

        $this->assertEquals('AbstractEventUnderTest', $event->getEventName());
    }

    public function testCreateThrowsExceptionWhenUnknownPropertySet()
    {
        $this->setExpectedException(
            RuntimeException::class,
            'Property "baz" is not a valid property on event "AbstractEventUnderTest"'
        );

        new AbstractEventUnderTest(['baz' => 'value']);
    }
}

class AbstractEventUnderTest extends AbstractEvent
{}
