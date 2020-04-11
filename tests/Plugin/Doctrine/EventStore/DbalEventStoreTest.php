<?php

declare(strict_types=1);

namespace CQRSTest\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Domain\Message\Metadata;
use CQRS\Plugin\Doctrine\EventStore\Config;
use CQRS\Plugin\Doctrine\EventStore\DbalEventStore;
use CQRS\Plugin\Doctrine\EventStore\CreateEventTableListener;
use CQRSTest\EventStore\SomeEvent;
use CQRSTest\EventStore\SomeSerializer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidType;

class DbalEventStoreTest extends TestCase
{
    private Connection $conn;

    private DbalEventStore $dbalEventStore;

    public function setUp(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension is not available.');
        }

        $conn = [
            'driver' => 'pdo_sqlite',
            'memory' => true
        ];
        $em = EntityManager::create($conn, Setup::createAnnotationMetadataConfiguration([]));

        $config = new Config();
        $listener = new CreateEventTableListener($config);
        $em->getEventManager()->addEventSubscriber($listener);

        Type::hasType(UuidType::NAME) || Type::addType(UuidType::NAME, UuidType::class);

        $tool = new SchemaTool($em);
        $tool->createSchema([]);

        $this->conn = $em->getConnection();
        //$this->conn->getDatabasePlatform()->registerDoctrineTypeMapping();

        $this->dbalEventStore = new DbalEventStore($config, $this->conn, new SomeSerializer());
    }

    public function testStoreEvent(): void
    {
        $aggregateId = 123;

        $event = new GenericDomainEventMessage('SomeAggregate', $aggregateId, 4, new SomeEvent());

        $this->dbalEventStore->store($event);

        $data = $this->conn->fetchAll('SELECT * FROM cqrs_event');

        $this->assertEquals(1, count($data));
        $this->assertEquals($event->getId(), $data[0]['event_id']);
        $this->assertEquals($event->getTimestamp()->format('Y-m-d H:i:s.u'), $data[0]['event_date']);
        $this->assertEquals('SomeAggregate', $data[0]['aggregate_type']);
        $this->assertEquals(123, $data[0]['aggregate_id']);
        $this->assertEquals(4, $data[0]['sequence_number']);
        $this->assertEquals(SomeEvent::class, $data[0]['payload_type']);
        $this->assertEquals('{}', $data[0]['payload']);
        $this->assertEquals('{}', $data[0]['metadata']);
    }

    public function testReadEvents(): void
    {
        for ($i = 1; $i <= 13; $i++) {
            $this->conn->insert('cqrs_event', [
                'event_id'        => 'bd0a32dd-37f1-42ab-807f-c3c29261a9fe',
                'event_date'      => '2014-08-15 09:55:33.054321',
                'aggregate_type'  => 'SomeAggregate',
                'aggregate_id'    => 123,
                'sequence_number' => $i + 10,
                'payload_type'    => SomeEvent::class,
                'payload'         => '{}',
                'metadata'        => '{}'
            ]);
        }

        $events = $this->dbalEventStore->read(-1);

        $this->assertCount(3, $events);
        $this->assertEquals([11, 12, 13], array_keys($events));
        $this->assertContainsOnlyInstancesOf(GenericDomainEventMessage::class, $events);

        $this->assertEquals('bd0a32dd-37f1-42ab-807f-c3c29261a9fe', (string) $events[11]->getId());
        $this->assertEquals('2014-08-15 09:55:33.054321', $events[11]->getTimestamp()->format('Y-m-d H:i:s.u'));
        $this->assertEquals('SomeAggregate', $events[11]->getAggregateType());
        $this->assertEquals(123, $events[11]->getAggregateId());
        $this->assertEquals(21, $events[11]->getSequenceNumber());
        $this->assertInstanceOf(SomeEvent::class, $events[11]->getPayload());
        $this->assertInstanceOf(Metadata::class, $events[11]->getMetadata());

        $events = $this->dbalEventStore->read(5, 5);
        $this->assertCount(5, $events);
    }
}
