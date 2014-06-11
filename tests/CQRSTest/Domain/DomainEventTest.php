<?php

namespace CQRSTest\Domain;

use CQRS\Domain\AggregateRoot;
use CQRS\Domain\DomainEvent;

class DomainEventTest extends \PHPUnit_Framework_TestCase
{
    public function testDomainEventSetsAggregateOnConstruction()
    {
        $aggregate = new TestedDomainEventAggregate();
        $event = $aggregate->test();

        $this->assertEquals('CQRSTest\Domain\TestedDomainEventAggregate', $event->aggregateType);
        $this->assertEquals(4, $event->aggregateId);
    }

    public function testConstructionOfDomainEventOutsideAggregateRootThrowsException()
    {
        $this->setExpectedException(
            'CQRS\Exception\RuntimeException',
            'DomainEvent can be created only from within an aggregate root'
        );

        new TestedDomainEvent();
    }
}

class TestedDomainEvent extends DomainEvent
{}

class TestedDomainEventAggregate extends AggregateRoot
{
    public function test()
    {
        return new TestedDomainEvent();
    }

    public function getId()
    {
        $id = new \stdClass();
        $id->id = 4;
        return $id;
    }
}
