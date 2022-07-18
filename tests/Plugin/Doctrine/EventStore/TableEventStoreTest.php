<?php

declare(strict_types=1);

namespace CQRSTest\Plugin\Doctrine\EventStore;

use CQRS\Domain\Message\GenericDomainEventMessage;
use CQRS\Plugin\Doctrine\EventStore\TableEventStore;
use CQRS\Plugin\Doctrine\EventStore\CreateEventStoreTableListener;
use CQRSTest\EventStore\SomeEvent;
use CQRSTest\EventStore\SomeSerializer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;

class TableEventStoreTest extends TestCase
{
    private Connection $conn;

    private TableEventStore $eventStore;

    public function setUp(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('The pdo_sqlite extension is not available.');
        }

        $config = ORMSetup::createAttributeMetadataConfiguration([], true);
        $this->conn = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        $em = EntityManager::create($this->conn, $config);

        $listener = new CreateEventStoreTableListener();
        $em->getEventManager()->addEventSubscriber($listener);

        $tool = new SchemaTool($em);
        $tool->createSchema([]);

        $this->eventStore = new TableEventStore(new SomeSerializer(), $this->conn, 'cqrs_event');
    }

    public function testStoreEvent(): void
    {
        $event = new GenericDomainEventMessage('SomeAggregate', 123, 4, new SomeEvent());

        $this->eventStore->store($event);

        $data = $this->conn->fetchAllAssociative('SELECT * FROM cqrs_event');

        self::assertCount(1, $data);
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
}
