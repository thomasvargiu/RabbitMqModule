<?php

return [
    'service_manager' => [
        'invokables' => [
            'RabbitMqModule\\Service\\RabbitMqService' => 'RabbitMqModule\\Service\\RabbitMqService',
            'RabbitMqModule\\Options\\Connection\\ConnectionOptionsFactory' => 'RabbitMqModule\\Options\\Connection\\ConnectionOptionsFactory',
            'RabbitMqModule\\Service\\Connection\\ConnectionFactory' =>
                'RabbitMqModule\\Service\\Connection\\ConnectionFactory'
        ],
        'factories' => [

        ],
        'abstract_factories' => [
            'RabbitMqModule\\Service\\AbstractServiceFactory' => 'RabbitMqModule\\Service\\AbstractServiceFactory'
        ]
    ],
    'rabbitmq' => [
        'connection' => [],
        'exchange' => [],
        'producer' => [],
        'consumer' => []
    ],
    'rabbitmq_factories' => [
        'connection' => 'RabbitMqModule\\Service\\ConnectionFactory',
        'producer' => 'RabbitMqModule\\Service\\ProducerFactory',
    ]
];
