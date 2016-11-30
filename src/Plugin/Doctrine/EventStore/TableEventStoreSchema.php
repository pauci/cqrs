<?php

namespace CQRS\Plugin\Doctrine\EventStore;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;

class TableEventStoreSchema
{
    /**
     * @var string
     */
    private $table;

    /**
     * @param string $table
     */
    public function __construct($table = 'cqrs_event')
    {
        $this->table = $table;
    }

    /**
     * @param bool $eventDateIndex
     * @param bool $aggregateIndex
     * @param bool $uniqueAggregateIndex
     * @return Table
     */
    public function getTableSchema($eventDateIndex = false, $aggregateIndex = false, $uniqueAggregateIndex = false)
    {
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
