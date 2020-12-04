<?php

declare(strict_types=1);

namespace CQRSTest\Domain\Model;

use CQRS\Domain\Message\GenericDomainEventMessage;
use PHPUnit\Framework\TestCase;

class AbstractAggregateRootTest extends TestCase
{
    public function testRegisterEvent(): void
    {
        $event = new SomeEvent();

        $aggregateRoot = new AggregateRootUnderTest(4);
        $aggregateRoot->raise($event);

        $eventMessages = $aggregateRoot->getUncommittedEvents();

        self::assertCount(1, $eventMessages);
        self::assertInstanceOf(GenericDomainEventMessage::class, $eventMessages[0]);
        self::assertEquals(AggregateRootUnderTest::class, $eventMessages[0]->getAggregateType());
        self::assertEquals(4, $eventMessages[0]->getAggregateId());
        self::assertSame($event, $eventMessages[0]->getPayload());

        $aggregateRoot->commitEvents();
        self::assertEmpty($aggregateRoot->getUncommittedEvents());
    }
}
