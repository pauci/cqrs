<?php
return [
    'cqrs' => [
        'commandHandlerLocator' => [
            'cqrs_default' => [
                'class'    => 'CQRS\Plugin\Zend\CommandHandling\ServiceCommandHandlerLocator',
                'handlers' => [
                    /**
                     * Command handlers in format:
                     *
                     * '<CommandType>' => <CommandHandlingCallback>,
                     *
                     * or:
                     *
                     * '<CommandHandlingServiceName>' => [
                     *      '<CommandType1>',
                     *      '<CommandType2>',
                     *      ...
                     * ]
                     */
                ]
            ]
        ],

        'commandBus' => [
            'cqrs_default' => [
                'class'          => 'CQRS\CommandHandling\SequentialCommandBus',
                'command_handler_locator' => 'cqrs_default',
                'transaction_manager'     => 'cqrs_default'
            ]
        ],

        'transactionManager' => [
            'cqrs_default' => [
                'class'  => 'CQRS\CommandHandling\NoTransactionManager'
            ]
        ]
    ],

    'cqrs_factories' => [
        'commandHandlerLocator' => 'CQRS\Plugin\Zend\Service\CommandHandlerLocatorFactory',
        'transactionManager'    => 'CQRS\Plugin\Zend\Service\TransactionManagerFactory',
        'commandBus'            => 'CQRS\Plugin\Zend\Service\CommandBusFactory',
    ],

    'service_manager' => [
        'abstract_factories' => [
            'CQRS' => 'CQRS\Plugin\Zend\ServiceFactory\AbstractCqrsServiceFactory',
        ],
    ],

    'doctrine' => [
        'driver' => [
            'CQRS_Driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Domain/SuperType'
                ],
            ],
        ]
    ]
];
