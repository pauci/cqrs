<?php

namespace CQRSTest\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\Plugin\Doctrine\EventStore\TableEventStore;
use CQRS\Plugin\Doctrine\EventStore\TableEventStoreSchema;
use CQRSTest\EventStore\SomeEvent;
use CQRSTest\EventStore\SomeSerializer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit_Framework_TestCase;

class TableEventStoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var TableEventStore
     */
    private $tableEventStore;

    public function setUp()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The Redis extension is not available.');
        }

        $schema = new TableEventStoreSchema();
        $tableSchema = $schema->getTableSchema();

        $this->conn = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true
        ]);
        $this->conn->getSchemaManager()->createTable($tableSchema);

        $this->tableEventStore = new TableEventStore(new SomeSerializer(), $this->conn, $tableSchema->getName());
    }

    public function testStoreEvent()
    {
        $aggregateId = 123;

        $event = new GenericDomainEventMessage('SomeAggregate', $aggregateId, 4, new SomeEvent());

        $this->tableEventStore->store($event);

        $data = $this->conn->fetchAll('SELECT * FROM cqrs_event');

        $this->assertEquals(1, count($data));
        $this->assertEquals($event->getId(), $data[0]['event_id']);
        $this->assertEquals($event->getTimestamp()->format('Y-m-d H:i:s'), $data[0]['event_date']);
        $this->assertEquals($event->getTimestamp()->format('u'), $data[0]['event_date_u']);
        $this->assertEquals('SomeAggregate', $data[0]['aggregate_type']);
        $this->assertEquals(123, $data[0]['aggregate_id']);
        $this->assertEquals(4, $data[0]['sequence_number']);
        $this->assertEquals(SomeEvent::class, $data[0]['payload_type']);
        $this->assertEquals('{}', $data[0]['payload']);
        $this->assertEquals('{}', $data[0]['metadata']);
    }

    public function testReadEvents()
    {
        for ($i = 1; $i <= 13; $i++) {
            $this->conn->insert('cqrs_event', [
                'event_id'        => 'bd0a32dd-37f1-42ab-807f-c3c29261a9fe',
                'event_date'      => '2014-08-15 09:55:33',
                'event_date_u'    => 654321,
                'aggregate_type'  => 'SomeAggregate',
                'aggregate_id'    => 123,
                'sequence_number' => $i + 10,
                'payload_type'    => SomeEvent::class,
                'payload'         => '{}',
                'metadata'        => '{}'
            ]);
        }

        $events = $this->tableEventStore->read();

        $this->assertCount(3, $events);
        $this->assertEquals([11, 12, 13], array_keys($events));
        $this->assertContainsOnlyInstancesOf(GenericDomainEventMessage::class, $events);

        $this->assertEquals('bd0a32dd-37f1-42ab-807f-c3c29261a9fe', (string) $events[11]->getId());
        $this->assertEquals('2014-08-15 09:55:33.654321', $events[11]->getTimestamp()->format('Y-m-d H:i:s.u'));
        $this->assertEquals('SomeAggregate', $events[11]->getAggregateType());
        $this->assertEquals(123, $events[11]->getAggregateId());
        $this->assertEquals(21, $events[11]->getSequenceNumber());
        $this->assertInstanceOf(SomeEvent::class, $events[11]->getPayload());
        $this->assertInstanceOf(Metadata::class, $events[11]->getMetadata());

        $events = $this->tableEventStore->read(5, 5);
        $this->assertCount(5, $events);
    }
}
