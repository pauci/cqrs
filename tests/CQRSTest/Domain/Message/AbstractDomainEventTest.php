<?php

namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\AbstractDomainEvent;
use CQRS\Domain\Model\AggregateRootInterface;
use PHPUnit_Framework_TestCase;

class AbstractDomainEventTest extends PHPUnit_Framework_TestCase
{
    public function testItSetsAggregatePassedViaConstructor()
    {
        $event = new AbstractDomainEventUnderTest([], new AbstractDomainEventTestAggregate());

        $this->assertEquals(AbstractDomainEventTestAggregate::class, $event->getAggregateType());
    }

    public function testGetAggregateType()
    {
        $event = new AbstractDomainEventUnderTest();
        $event->setAggregate(new AbstractDomainEventTestAggregate());

        $this->assertEquals(AbstractDomainEventTestAggregate::class, $event->getAggregateType());
    }

    public function testItReturnsLatePopulatedAggregateId()
    {
        $aggregate = new AbstractDomainEventTestAggregate();

        $event = new AbstractDomainEventUnderTest();
        $event->setAggregate($aggregate);

        $this->assertEquals(null, $event->getAggregateId());

        // Populate aggregate id
        $aggregate->id = 123;
        $this->assertEquals(123, $event->getAggregateId());
    }
}

class AbstractDomainEventUnderTest extends AbstractDomainEvent
{}

class AbstractDomainEventTestAggregate implements AggregateRootInterface
{
    public $id;

    public function getId()
    {
        return $this->id;
    }

    public function pullDomainEvents()
    {}

    public function isDeleted()
    {}
}
