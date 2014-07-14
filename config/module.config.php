<?php
return [
    'cqrs' => [
        'commandBus' => [
            'cqrs_default' => [
                'class'                 => 'CQRS\CommandHandling\SequentialCommandBus',
                'commandHandlerLocator' => 'cqrs_default',
                'transactionManager'    => 'cqrs_default',
            ]
        ],

        'commandHandlerLocator' => [
            'cqrs_default' => [
                'class'    => 'CQRS\Plugin\Zend\CommandHandling\ServiceCommandHandlerLocator',
                'handlers' => [
                    /**
                     * CommandInterface handlers in format:
                     *
                     *  '<CommandType>' => '<CommandHandlerServiceName>',
                     *
                     * or:
                     *
                     *  '<CommandHandlerServiceName>' => [
                     *      '<CommandType1>',
                     *      '<CommandType2>',
                     *      ...
                     *  ]
                     */
                ],
            ]
        ],

        'transactionManager' => [
            'cqrs_default' => [
                'class' => 'CQRS\CommandHandling\NoTransactionManager',
                /**
                 * To use with doctrine:
                 *
                 *  'class'          => 'CQRS\Plugin\Doctrine\CommandHandling\ImplicitOrmTransactionManager',
                 *  'entity_manager' => 'orm_default',
                 */
            ]
        ],

        'eventPublisher' => [
            'cqrs_default' => [
                'class'    => 'CQRS\EventHandling\SimpleEventPublisher',
                'eventBus' => 'cqrs_default'
            ]
        ],

        'eventBus' => [
            'cqrs_default' => [
                'class'               => 'CQRS\EventHandling\SynchronousEventBus',
                'eventHandlerLocator' => 'cqrs_default',
                'eventStore'          => 'cqrs_default',
            ]
        ],

        'eventHandlerLocator' => [
            'cqrs_default' => [
                'class'    => 'CQRS\Plugin\Zend\EventHandling\ServiceEventHandlerLocator',
                'services' => [
                    /**
                     * Example:
                     *
                     *  '<ServiceName>' => [
                     *      '<EventName1>',
                     *      '<EventName2>',
                     *      ...
                     *  ]
                     *
                     * or:
                     *
                     *  [
                     *      'event'    => ['<EventName1>', ...],
                     *      'service'  => ['<ServiceName1', ...],
                     *      'priority' => 1
                     *  ]
                     */
                ],
                'callbacks' => [
                    /**
                     * Example:
                     *
                     *  '<EventName>' => function($event) {},
                     *
                     * or:
                     *
                     *  [
                     *      'event'    => ['<EventName1>, '<EventName2'],
                     *      'callback' => function($event) {},
                     *      'priority' => 1
                     *  ]
                     */
                ],
                'subscribers' => [
                    /**
                     * Array of instances
                     */
                ],
            ]
        ],

        'eventStore' => [
            'cqrs_default' => [
            ]
        ]
    ],

    'cqrs_factories' => [
        'commandBus'            => 'CQRS\Plugin\Zend\Service\CommandBusFactory',
        'commandHandlerLocator' => 'CQRS\Plugin\Zend\Service\CommandHandlerLocatorFactory',
        'transactionManager'    => 'CQRS\Plugin\Zend\Service\TransactionManagerFactory',

        'eventPublisher'        => 'CQRS\Plugin\Zend\Service\EventPublisherFactory',
        'eventBus'              => 'CQRS\Plugin\Zend\Service\EventBusFactory',
        'eventHandlerLocator'   => 'CQRS\Plugin\Zend\Service\EventHandlerLocatorFactory',
        'eventStore'            => 'CQRS\Plugin\Zend\Service\EventStoreFactory',
    ],

    'service_manager' => [
        'abstract_factories' => [
            'CQRS' => 'CQRS\Plugin\Zend\ServiceFactory\AbstractCqrsServiceFactory',
        ]
    ],

    'doctrine' => [
        'driver' => [
            'CQRS_Driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Domain'
                ]
            ]
        ],
        'configuration' => [
            'orm_default' => [
                'types' => [
                    'uuid'        => 'Rhumsaa\Uuid\Doctrine\UuidType',
                    'binary_uuid' => 'CQRS\Plugin\Doctrine\Type\BinaryUuidType'
                ]
            ]
        ],
        /*
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    'CQRS\Plugin\Doctrine\Domain\AggregateRootMetadataListener'
                ]
            ]
        ]
        */
    ]
];
