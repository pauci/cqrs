<?php

declare(strict_types=1);

namespace CQRS\Plugin\Doctrine\EventStore;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;

class TableEventStoreSchema
{
    private string $table;

    public function __construct(string $table = 'cqrs_event')
    {
        $this->table = $table;
    }

    public function getTableSchema(
        bool $eventDateIndex = false,
        bool $aggregateIndex = false,
        bool $uniqueAggregateIndex = false
    ): Table {
        $schema = new Schema();
        $table = $schema->createTable($this->table);
        $table->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('event_id', 'string', ['length' => 36, 'fixed' => true, 'notnull' => true]);
        $table->addColumn('event_date', 'datetime', ['notnull' => true]);
        $table->addColumn('event_date_u', 'integer', ['unsigned' => true, 'notnull' => true]);
        $table->addColumn('aggregate_type', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('aggregate_id', 'binary', ['length' => 36, 'notnull' => false]);
        $table->addColumn('sequence_number', 'integer', ['unsigned' => true, 'notnull' => false]);
        $table->addColumn('payload_type', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('payload', 'text');
        $table->addColumn('metadata', 'text');
        $table->setPrimaryKey(['id']);
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
