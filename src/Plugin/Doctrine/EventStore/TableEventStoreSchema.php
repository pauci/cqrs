<?php

namespace CQRS\Plugin\Doctrine\EventStore;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;

class TableEventStoreSchema
{
    /** @var string */
    private $table;

    /**
     * @param string $table
     */
    public function __construct($table = 'cqrs_event')
    {
        $this->table = $table;
    }

    /**
     * @return Table
     */
    public function getTableSchema()
    {
        $schema = new Schema();
        $table = $schema->createTable($this->table);
        $table->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('event_id', 'string', ['notnull' => true]);
        $table->addColumn('event_date', 'datetime', ['notnull' => true]);
        $table->addColumn('event_date_u', 'integer', ['notnull' => true, 'unsigned' => true]);
        $table->addColumn('aggregate_type', 'string', ['notnull' => false]);
        $table->addColumn('aggregate_id', 'string', ['notnull' => false]);
        $table->addColumn('sequence_number', 'integer', ['notnull' => false, 'unsigned' => true]);
        $table->addColumn('payload_type', 'string', ['notnull' => true]);
        $table->addColumn('payload', 'text');
        $table->addColumn('metadata', 'text');
        $table->setPrimaryKey(['id']);
        $table->addIndex(['aggregate_type', 'aggregate_id']);
        return $table;
    }
}
