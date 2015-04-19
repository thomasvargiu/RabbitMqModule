<?php

return [
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
    ],
    'rabbitmq' => [
        'connection' => [
            'default' => []
        ],
        'exchange' => [],
        'producer' => [],
        'consumer' => []
    ],
    'rabbitmq_factories' => [
        'connection' => 'RabbitMqModule\\Service\\ConnectionFactory',
        'producer' => 'RabbitMqModule\\Service\\ProducerFactory',
        'consumer' => 'RabbitMqModule\\Service\\ConsumerFactory'
    ]
];
