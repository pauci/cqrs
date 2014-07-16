<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\GenericDomainEvent;

class GenericDomainEventTest extends \PHPUnit_Framework_TestCase
{
    public function testThrowsExceptionOnNonExistent()
    {
        $this->setExpectedException('RuntimeException');

        $event = new GenericDomainEvent('TestEvent', ['foo' => 'bar']);
        $event->baz;
    }

    public function testEventNameIsDynamic()
    {
        $event = new GenericDomainEvent('Test', ['foo' => 'bar']);

        $this->assertEquals('Test', $event->getEventName());
    }

    public function testGetAggregateIdIsNullAfterCreation()
    {
        $event = new GenericDomainEvent('Test', ['test' => 'value']);
        $this->assertNull($event->getAggregateId());
    }
} 
