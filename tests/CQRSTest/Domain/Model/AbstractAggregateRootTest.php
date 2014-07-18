<?php

namespace CQRSTest\Domain\Model;

use CQRS\Domain\Message\AbstractDomainEvent;
use CQRS\Domain\Model\AbstractAggregateRoot;
use CQRS\Domain\Model\AggregateRootInterface;
use PHPUnit_Framework_TestCase;

class AbstractAggregateRootTest extends PHPUnit_Framework_TestCase
{
    public function testRaiseAndPullDomainEvents()
    {
        $event = new RaiseDomainEventTestedEvent();

        $aggregateRoot = new AggregateRootUnderTest();
        $aggregateRoot->testRaiseDomainEvent($event);

        $this->assertSame($aggregateRoot, $event->aggregate);

        $this->assertSame([$event], $aggregateRoot->pullDomainEvents());

        $this->assertEmpty($aggregateRoot->pullDomainEvents());
    }
}

class AggregateRootUnderTest extends AbstractAggregateRoot
{
    public function testRaiseDomainEvent($event)
    {
        $this->raiseDomainEvent($event);
    }

    public function getId()
    {}
}

class RaiseDomainEventTestedEvent extends AbstractDomainEvent
{
    public $aggregate;

    public function setAggregate(AggregateRootInterface $aggregate)
    {
        $this->aggregate = $aggregate;
        return $this;
    }
}
