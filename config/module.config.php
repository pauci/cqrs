<?php
return [
    'service_manager' => [
        'abstract_factories' => [
            'DoctrineModule' => 'CQRS\ServiceFactory\AbstractCqrsServiceFactory',
        ],
    ],

    'cqrs_factories' => [
        'commandHandlerLocator' => 'CQRS\Service\CommandHandlerLocatorFactory',
        'commandBus'            => 'CQRS\Service\CommandBusFactory'
    ],

    'cqrs' => [
        'commandHandlerLocator' => [
            'cqrs_default' => [
                'class' => 'CQRS\Commanding\ServiceCommandHandlerLocator'
            ]
        ],

        'commandBus' => [
            'cqrs_default' => [
                'class'          => 'CQRS\Commanding\SequentialCommandBus',
                'entity_manager' => 'orm_default'
            ]
        ],
    ]
];
