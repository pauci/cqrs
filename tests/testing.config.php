<?php

return [
    'cqrs' => [
    ],

    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => [
                    'memory' => true,
                ],
            ]
        ]
    ]
];
