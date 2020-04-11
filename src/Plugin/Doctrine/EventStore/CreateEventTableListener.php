<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\EventStore;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
use Ramsey\Uuid\Doctrine\UuidType;

class CreateEventTableListener implements EventSubscriber
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getSubscribedEvents(): array
    {
        return [
            ToolEvents::postGenerateSchema,
        ];
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schema = $args->getSchema();

        $this->createEventTable($schema);
    }

    private function createEventTable(Schema $schema): void
    {
        $table = $schema->createTable($this->config->getTableName());

        $table->addColumn('id', Types::BIGINT, [
            'autoincrement' => true,
            'unsigned' => true,
        ]);
        $table->addColumn('event_id', UuidType::NAME);
        $table->addColumn('event_date', Types::DATETIME_MUTABLE, [
            'notnull' => true,
        ]);
        $table->addColumn('aggregate_type', Types::STRING, [
            'notnull' => false,
        ]);
        $table->addColumn('aggregate_id', Types::STRING, [
            'notnull' => false,
        ]);
        $table->addColumn('sequence_number', Types::INTEGER, [
            'unsigned' => true,
            'notnull' => false,
        ]);
        $table->addColumn('payload_type', Types::STRING, [
            'length' => 255,
        ]);
        $table->addColumn('payload', Types::JSON);
        $table->addColumn('metadata', Types::JSON);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['event_id']);
        $table->addIndex(['event_date']);
        $table->addUniqueIndex(['aggregate_type', 'aggregate_id', 'sequence_number']);
    }
}
