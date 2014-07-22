<?php

namespace CQRSTest\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\GenericDomainEvent;
use CQRS\Domain\Model\AbstractAggregateRoot;
use CQRS\Plugin\Doctrine\EventStore\TableEventStore;
use CQRS\Plugin\Doctrine\EventStore\TableEventStoreSchema;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit_Framework_TestCase;

class TableEventStoreTest extends PHPUnit_Framework_TestCase
{
    /** @var Connection */
    private $conn;

    /** @var TableEventStore */
    private $tableEventStore;

    public function setUp()
    {
        $serializer = $this->getMock('CQRS\Serializer\SerializerInterface');
        $serializer->expects($this->any())
            ->method('serialize')
            ->will($this->returnValue('{}'));
        /** @var \CQRS\Serializer\SerializerInterface $serializer */

        $schema = new TableEventStoreSchema();
        $tableSchema = $schema->getTableSchema();

        $this->conn = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true
        ]);
        $this->conn->getSchemaManager()->createTable($tableSchema);

        $this->tableEventStore = new TableEventStore($serializer, $this->conn, $tableSchema->getName());
    }

    public function testStoreEvent()
    {
        $event = new GenericDomainEvent('Test');

        $this->tableEventStore->store($event);

        $data = $this->conn->fetchAll('SELECT * FROM cqrs_domain_event');

        $this->assertEquals(1, count($data));
        $this->assertEquals($event->getId(), $data[0]['event_id']);
        $this->assertEquals($event->getTimestamp()->format('Y-m-d H:i:s'), $data[0]['event_date']);
        $this->assertEquals('Test', $data[0]['event_name']);
        $this->assertEquals('{}', $data[0]['data']);
    }

    public function testStoreEventWithAggregateIdContainingMultipleKeys()
    {
        $aggregate = $this->getMock(AbstractAggregateRoot::class);
        $aggregate->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(['id' => 43, 'name' => 'test name']));

        $event = new GenericDomainEvent('Test', [], $aggregate);

        $this->tableEventStore->store($event);

        $data = $this->conn->fetchAll('SELECT * FROM cqrs_domain_event');

        $this->assertEquals('{"id":43,"name":"test name"}', $data[0]['aggregate_id']);
    }

    public function testReadEvents()
    {
        for ($i = 1; $i <= 13; $i++) {
            $event = new GenericDomainEvent('Test');
            $this->tableEventStore->store($event);
        }

        $events = $this->tableEventStore->read(11, 5);

        $this->assertCount(3, $events);
        $this->assertEquals([
            11 => '{}',
            12 => '{}',
            13 => '{}',
        ], $events);
    }
}
