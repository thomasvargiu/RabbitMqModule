<?php

namespace RabbitMqModule;

use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use RabbitMqModule\Command;

return [
    ConfigAbstractFactory::class => [
        Command\ListConsumersCommand::class => [
            'config',
        ],
    ],
    'rabbitmq' => [
        'connection' => [
            'default' => []
        ],
        'producer' => [],
        'consumer' => [],
        'rpc_server' => [],
        'rpc_client' => []
    ],
    'rabbitmq_factories' => [
        'connection' => 'RabbitMqModule\\Service\\ConnectionFactory',
        'producer' => 'RabbitMqModule\\Service\\ProducerFactory',
        'consumer' => 'RabbitMqModule\\Service\\ConsumerFactory',
        'rpc_server' => 'RabbitMqModule\\Service\\RpcServerFactory',
        'rpc_client' => 'RabbitMqModule\\Service\\RpcClientFactory'
    ],
    'service_manager' => [
        'invokables' => [
            'RabbitMqModule\\Service\\RabbitMqService' => 'RabbitMqModule\\Service\\RabbitMqService',
            'RabbitMqModule\\Service\\Connection\\StreamConnectionFactory' =>
                'RabbitMqModule\\Service\\Connection\\StreamConnectionFactory',
            'RabbitMqModule\\Service\\Connection\\SslConnectionFactory' =>
                'RabbitMqModule\\Service\\Connection\\SslConnectionFactory',
            'RabbitMqModule\\Service\\Connection\\SocketConnectionFactory' =>
                'RabbitMqModule\\Service\\Connection\\SocketConnectionFactory',
            'RabbitMqModule\\Service\\Connection\\LazyConnectionFactory' =>
                'RabbitMqModule\\Service\\Connection\\LazyConnectionFactory'
        ],
        'abstract_factories' => [
            'RabbitMqModule\\Service\\AbstractServiceFactory' => 'RabbitMqModule\\Service\\AbstractServiceFactory'
        ],
        'factories' => [
            Command\ListConsumersCommand::class => ConfigAbstractFactory::class,
            Command\StartConsumerCommand::class => Command\Factory\ContainerAwareCommandFactory::class,
            Command\StartRpcServerCommand::class => Command\Factory\ContainerAwareCommandFactory::class,
            Command\PublishMessageCommand::class => Command\Factory\ContainerAwareCommandFactory::class,
            Command\SetupFabricCommand::class => Command\Factory\ContainerAwareCommandFactory::class,
        ],
    ],
    'laminas-cli' => [
        'commands' => [
            Command\ListConsumersCommand::getDefaultName() => Command\ListConsumersCommand::class,
            Command\StartConsumerCommand::getDefaultName() => Command\StartConsumerCommand::class,
            Command\StartRpcServerCommand::getDefaultName() => Command\StartRpcServerCommand::class,
            Command\PublishMessageCommand::getDefaultName() => Command\PublishMessageCommand::class,
            Command\SetupFabricCommand::getDefaultName() => Command\SetupFabricCommand::class,
        ]
    ]
];
