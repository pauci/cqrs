<?php
namespace CQRSTest\Domain\Message;

use CQRS\Domain\Message\AbstractDomainEventMessage;
use CQRS\Domain\Model\AggregateRootInterface;

class AbstractDomainEventMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAggregateAndGetItsTypeAndId()
    {
        $aggregate = new AbstractDomainEventMessageTestAggregate();

        $event = new AbstractDomainEventMessageUnderTest();
        $event->setAggregate($aggregate);

        $this->assertEquals('CQRSTest\Domain\Message\AbstractDomainEventMessageTestAggregate', $event->getAggregateType());
        $this->assertEquals(null, $event->getAggregateId());

        $aggregate->id = 4;
        $this->assertEquals(4, $event->getAggregateId());
    }
}

class AbstractDomainEventMessageUnderTest extends AbstractDomainEventMessage
{}

class AbstractDomainEventMessageTestAggregate implements AggregateRootInterface
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
