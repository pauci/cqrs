<?php

namespace CQRSTest\Domain;

use CQRS\Domain\AggregateRoot;
use CQRS\Domain\DomainEvent;

class AggregateRootTest extends \PHPUnit_Framework_TestCase
{
    public function testRaiseAndPullDomainEvents()
    {
        $aggregateRoot = new AggregateRootUnderTest();
        $aggregateRoot->test();

        $this->assertSame([$aggregateRoot->event], $aggregateRoot->pullDomainEvents());
        $this->assertEmpty($aggregateRoot->pullDomainEvents());
    }
}

class AggregateRootUnderTest extends AggregateRoot
{
    public $event;

    public function test()
    {
        $this->event = new AggregateRootTestEvent();
        $this->raiseDomainEvent($this->event);
    }

    public function getId()
    {}
}

class AggregateRootTestEvent extends DomainEvent
{
    public function __construct() {}
}
