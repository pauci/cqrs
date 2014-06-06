<?php
return [
    'cqrs' => [
        'commandBus' => [
            'cqrs_default' => [
                'class'                   => 'CQRS\CommandHandling\SequentialCommandBus',
                'command_handler_locator' => 'cqrs_default',
                'transaction_manager'     => 'cqrs_default',
            ]
        ],

        'commandHandlerLocator' => [
            'cqrs_default' => [
                'class'    => 'CQRS\Plugin\Zend\CommandHandling\ServiceCommandHandlerLocator',
                'handlers' => [
                    /**
                     * Command handlers in format:
                     *
                     * '<CommandType>' => '<CommandHandlerServiceName>',
                     *
                     * or:
                     *
                     * '<CommandHandlerServiceName>' => [
                     *      '<CommandType1>',
                     *      '<CommandType2>',
                     *      ...
                     * ]
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
                 * 'class'          => 'CQRS\Plugin\Doctrine\CommandHandling\ImplicitOrmTransactionManager',
                 * 'entity_manager' => 'orm_default',
                 */
            ]
        ],

        'eventPublisher' => [
            'cqrs_default' => [
                'class' => 'CQRS\EventHandling\SimpleEventPublisher'
            ]
        ],

        'eventBus' => [
            'cqrs_default' => [
                'class'                 => 'CQRS\EventHandling\SynchronousEventBus',
                'event_handler_locator' => 'cqrs_default',
                'event_store'           => 'cqrs_default',
            ]
        ],

        'eventHandlerLocator' => [
            'cqrs_default' => [
                'class'    => 'CQRS\Plugin\Zend\EventHandling\ServiceEventHandlerLocator',
                'handlers' => [
                    /**
                     * Event handlers in format:
                     *
                     * '<EventName>' => [
                     *      '<EventHandlerServiceName1>',
                     *      '<EventHandlerServiceName2>',
                     *      ...
                     * ]
                     */
                ],
            ]
        ],

        'eventStore' => [
            'cqrs_default' => [
                ''
            ]
        ]
    ],

    'cqrs_factories' => [
        'commandBus'            => 'CQRS\Plugin\Zend\Service\CommandBusFactory',
        'commandHandlerLocator' => 'CQRS\Plugin\Zend\Service\CommandHandlerLocatorFactory',
        'transactionManager'    => 'CQRS\Plugin\Zend\Service\TransactionManagerFactory',

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
                    __DIR__ . '/../src/Domain/SuperType'
                ]
            ]
        ]
    ]
];
