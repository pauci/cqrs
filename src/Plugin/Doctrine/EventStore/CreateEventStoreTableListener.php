<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\EventStore;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

class CreateEventStoreTableListener implements EventSubscriber
{
    private string $name;

    public function __construct(string $name = 'cqrs_event')
    {
        $this->name = $name;
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
        $table = $schema->createTable($this->name);

        $this->addColumns($table);
        $this->addIndexes($table);
    }

    private function addColumns(Table $table): void
    {
        $table->addColumn('id', 'bigint', [
            'autoincrement' => true,
            'unsigned' => true,
        ]);

        $table->addColumn('event_id', 'string', [
            'length' => 36,
            'fixed' => true,
            'notnull' => true,
        ]);

        $table->addColumn('event_date', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('event_date_u', 'integer', [
            'unsigned' => true,
            'notnull' => true,
        ]);

        $table->addColumn('aggregate_type', 'string', [
            'length' => 255,
            'notnull' => false,
        ]);

        $table->addColumn('aggregate_id', 'string', [
            'length' => 255,
            'notnull' => false,
        ]);

        $table->addColumn('sequence_number', 'integer', [
            'unsigned' => true,
            'notnull' => false,
        ]);

        $table->addColumn('payload_type', 'string', [
            'length' => 255,
            'notnull' => true,
        ]);

        $table->addColumn('payload', 'text');
        $table->addColumn('metadata', 'text');
    }

    private function addIndexes(Table $table): void
    {
        $table->setPrimaryKey(['id']);
        $table->addIndex(['event_date', 'event_date_u']);
        $table->addUniqueIndex(['aggregate_type', 'aggregate_id', 'sequence_number']);
    }

    public function getTableSchema(
        bool $eventDateIndex = false,
        bool $aggregateIndex = false,
        bool $uniqueAggregateIndex = false
    ): Table {
        $schema = new Schema();
        $table = $schema->createTable($this->name);

        if ($eventDateIndex) {
            $table->addIndex(['event_date', 'event_date_u']);
        }

        if ($aggregateIndex) {
            if ($uniqueAggregateIndex) {
                $table->addUniqueIndex(['aggregate_type', 'aggregate_id', 'sequence_number']);
            } else {
                $table->addIndex(['aggregate_type', 'aggregate_id', 'sequence_number']);
            }
        }

        return $table;
    }
}
