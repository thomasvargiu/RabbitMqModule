<?php

namespace RabbitMqModule;

return [
    'rabbitmq' => [
        'connection' => [
            'default' => []
        ],
        'producer' => [],
        'consumer' => []
    ],
    'rabbitmq_factories' => [
        'connection' => 'RabbitMqModule\\Service\\ConnectionFactory',
        'producer' => 'RabbitMqModule\\Service\\ProducerFactory',
        'consumer' => 'RabbitMqModule\\Service\\ConsumerFactory'
    ],
    'console' => [
        'router' => [
            'routes' => [
                'rabbitmq_module-setup-fabric' => [
                    'options' => [
                        'route'    => 'rabbitmq setup-fabric',
                        'defaults' => [
                            'controller' => __NAMESPACE__ . '\\Controller\\SetupFabric',
                            'action' => 'index'
                        ]
                    ]
                ],
                'rabbitmq_module-consumer' => [
                    'options' => [
                        'route'    => 'rabbitmq consumer <name> [--without-signals|-w]',
                        'defaults' => [
                            'controller' => __NAMESPACE__ . '\\Controller\\Consumer',
                            'action' => 'index'
                        ]
                    ]
                ],
                'rabbitmq_module-stdin-producer' => [
                    'options' => [
                        'route'    => 'rabbitmq stdin-producer <name> [--route=] <msg>',
                        'defaults' => [
                            'controller' => __NAMESPACE__ . '\\Controller\\StdInProducer',
                            'action' => 'index'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            __NAMESPACE__ . '\\Controller\\SetupFabric' => __NAMESPACE__ . '\\Controller\\SetupFabricController',
            __NAMESPACE__ . '\\Controller\\Consumer' => __NAMESPACE__ . '\\Controller\\ConsumerController',
            __NAMESPACE__ . '\\Controller\\StdInProducer' => __NAMESPACE__ . '\\Controller\\StdInProducerController'
        ]
    ],
    'service_manager' => [
        'invokables' => [
            'RabbitMqModule\\Service\\RabbitMqService' => 'RabbitMqModule\\Service\\RabbitMqService',

            'RabbitMqModule\\Service\\Connection\\StreamConnectionFactory' =>
                'RabbitMqModule\\Service\\Connection\\StreamConnectionFactory',
            'RabbitMqModule\\Service\\Connection\\SslConnectionFactory' =>
                'RabbitMqModule\\Service\\Connection\\SslConnectionFactory',
            'RabbitMqModule\\Service\\Connection\\SocketConnectionFactory' =>
                'RabbitMqModule\\Service\\Connection\\SocketConnectionFactory'
        ],
        'factories' => [

        ],
        'abstract_factories' => [
            'RabbitMqModule\\Service\\AbstractServiceFactory' => 'RabbitMqModule\\Service\\AbstractServiceFactory'
        ]
    ]
];
