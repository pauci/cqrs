<?php

namespace CQRSTest\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\GenericDomainEvent;
use CQRS\Domain\Model\AbstractAggregateRoot;
use CQRS\Plugin\Doctrine\EventStore\TableEventStore;
use CQRS\Plugin\Doctrine\EventStore\TableEventStoreSchema;
use Doctrine\DBAL\DriverManager;
use PHPUnit_Framework_TestCase;

class TableEventStoreTest extends PHPUnit_Framework_TestCase
{
    public function testStoreEvent()
    {
        $serializer = $this->getMock('CQRS\Serializer\SerializerInterface');
        $serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{}'));

        $schema = new TableEventStoreSchema();
        $tableSchema = $schema->getTableSchema();

        $conn = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true
        ]);
        $conn->getSchemaManager()->createTable($tableSchema);

        $eventStore = new TableEventStore($serializer, $tableSchema->getName(), $conn);

        $event = new GenericDomainEvent('Test');

        $eventStore->store($event);

        $data = $conn->fetchAll('SELECT * FROM cqrs_domain_event');

        $this->assertEquals(1, count($data));
        $this->assertEquals($event->getId(), $data[0]['event_id']);
        $this->assertEquals($event->getTimestamp()->format('Y-m-d H:i:s'), $data[0]['event_date']);
        $this->assertEquals('Test', $data[0]['event_name']);
        $this->assertEquals('{}', $data[0]['data']);
    }

    public function testStoreEventWithAggregateIdContainingMultipleKeys()
    {
        $serializer = $this->getMock('CQRS\Serializer\SerializerInterface');
        $serializer->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('{}'));

        $schema = new TableEventStoreSchema();
        $tableSchema = $schema->getTableSchema();

        $conn = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true
        ]);
        $conn->getSchemaManager()->createTable($tableSchema);

        $eventStore = new TableEventStore($serializer, $tableSchema->getName(), $conn);

        $aggregate = $this->getMock(AbstractAggregateRoot::class);
        $aggregate->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(['id' => 43, 'name' => 'test name']));

        $event = new GenericDomainEvent('Test', [], $aggregate);

        $eventStore->store($event);

        $data = $conn->fetchAll('SELECT * FROM cqrs_domain_event');

        $this->assertEquals('{"id":43,"name":"test name"}', $data[0]['aggregate_id']);
    }

    public function testReadEvents()
    {
        $serializer = $this->getMock('CQRS\Serializer\SerializerInterface');
        $serializer->expects($this->any())
            ->method('serialize')
            ->will($this->returnValue('{}'));
        $serializer->expects($this->any())
            ->method('deserialize')
            ->will($this->returnValue('event'));

        $schema = new TableEventStoreSchema();
        $tableSchema = $schema->getTableSchema();

        $conn = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true
        ]);
        $conn->getSchemaManager()->createTable($tableSchema);

        $eventStore = new TableEventStore($serializer, $tableSchema->getName(), $conn);

        for ($i = 1; $i <= 13; $i++) {
            $event = new GenericDomainEvent('Test');
            $eventStore->store($event);
        }

        $events = $eventStore->readPage(5);

        $this->assertCount(3, $events);
        $this->assertEquals([
            11 => '{}',
            12 => '{}',
            13 => '{}',
        ], $events);
    }
}
