<?php

namespace CQRSTest\EventHandling;

use CQRS\EventHandling\DefaultDomainEvent;

class DefaultDomainEventTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateArrayMapsToProperties()
    {
        $event = new TestDefaultDomainEvent([
            'test'          => 'value',
            'protectedTest' => 'protectedValue'
        ]);

        $this->assertEquals('value', $event->test);
        $this->assertEquals('protectedValue', $event->protectedTest);
    }

    public function testCreateThrowsExceptionWhenUnknownPropertySet()
    {
        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'Property "unknown" is not a valid property on event "TestDefaultDomain"'
        );

        new TestDefaultDomainEvent(['unknown' => 'value']);
    }

    public function testAccessingUndefinedPropertyThrowsException()
    {
        $event = new TestDefaultDomainEvent();

        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'Property "unknown" is not a valid property on event "TestDefaultDomain"'
        );

        $value = $event->unknown;
    }
}

/**
 * @property-read string $protectedTest
 */
class TestDefaultDomainEvent extends DefaultDomainEvent
{
    public $test;
    protected $protectedTest;
}
