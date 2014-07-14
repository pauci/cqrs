<?php

namespace CQRS\Plugin\Doctrine\EventStore;

class Config
{
    /** @var string */
    public $tableName = 'event';

    /** @var string */
    public $eventIdColumn = 'id';

    /** @var string */
    public $aggregateTypeColumn = 'aggregate_type';

    /** @var string */
    public $aggregateIdColumn = 'aggregate_id';

    /** @var string */
    public $eventNameColumn = 'event_name';

    /** @var string */
    public $eventDataColumn = 'event_data';

    /** @var string */
    public $timestampColumn = 'occurred_at';
} 
