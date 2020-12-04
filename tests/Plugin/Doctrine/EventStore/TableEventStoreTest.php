<?php

declare(strict_types=1);

namespace CQRSTest\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\Plugin\Doctrine\EventStore\TableEventStore;
use CQRS\Plugin\Doctrine\EventStore\TableEventStoreSchema;
use CQRSTest\EventStore\SomeEvent;
use CQRSTest\EventStore\SomeSerializer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

class TableEventStoreTest extends TestCase
{
    private Connection $conn;

    private TableEventStore $tableEventStore;

    public function setUp(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('The pdo_sqlite extension is not available.');
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

    public function testStoreEvent(): void
    {
        $aggregateId = 123;

        $event = new GenericDomainEventMessage('SomeAggregate', $aggregateId, 4, new SomeEvent());

        $this->tableEventStore->store($event);

        $data = $this->conn->fetchAll('SELECT * FROM cqrs_event');

        self::assertEquals(1, count($data));
        self::assertEquals($event->getId(), $data[0]['event_id']);
        self::assertEquals($event->getTimestamp()->format('Y-m-d H:i:s'), $data[0]['event_date']);
        self::assertEquals((int) $event->getTimestamp()->format('u'), $data[0]['event_date_u']);
        self::assertEquals('SomeAggregate', $data[0]['aggregate_type']);
        self::assertEquals(123, $data[0]['aggregate_id']);
        self::assertEquals(4, $data[0]['sequence_number']);
        self::assertEquals(SomeEvent::class, $data[0]['payload_type']);
        self::assertEquals('{}', $data[0]['payload']);
        self::assertEquals('{}', $data[0]['metadata']);
    }

    public function testReadEvents(): void
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

        $events = $this->tableEventStore->read(-1);

        self::assertCount(3, $events);
        self::assertEquals([11, 12, 13], array_keys($events));
        self::assertContainsOnlyInstancesOf(GenericDomainEventMessage::class, $events);

        self::assertEquals('bd0a32dd-37f1-42ab-807f-c3c29261a9fe', (string) $events[11]->getId());
        self::assertEquals('2014-08-15 09:55:33.654321', $events[11]->getTimestamp()->format('Y-m-d H:i:s.u'));
        self::assertEquals('SomeAggregate', $events[11]->getAggregateType());
        self::assertEquals(123, $events[11]->getAggregateId());
        self::assertEquals(21, $events[11]->getSequenceNumber());
        self::assertInstanceOf(SomeEvent::class, $events[11]->getPayload());
        self::assertInstanceOf(Metadata::class, $events[11]->getMetadata());

        $events = $this->tableEventStore->read(5, 5);
        self::assertCount(5, $events);
    }
}
