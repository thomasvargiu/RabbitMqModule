<?php
declare(strict_types=1);

namespace RabbitMqModule;

use Laminas\ServiceManager\Factory\InvokableFactory;
use RabbitMqModule\Command;
use RabbitMqModule\Service;
use RabbitMqModule\Service\AbstractFactory;

/**
 * @psalm-import-type ConnectionOptions from Options\Connection
 * @psalm-import-type ExchangeBindOptions from Options\ExchangeBind
 * @psalm-import-type ExchangeOptions from Options\Exchange
 * @psalm-import-type QueueOptions from Options\Queue
 * @psalm-import-type ProducerOptions from Options\Producer
 * @psalm-import-type QosOptions from Options\Qos
 * @psalm-import-type ConsumerOptions from Options\Consumer
 * @psalm-import-type RpcClientOptions from Options\RpcClient
 * @psalm-import-type RpcServerOptions from Options\RpcServer
 * @psalm-type ConfigArray = array{
 *   rabbitmq_factories: array{
 *     connection: class-string<AbstractFactory>,
 *     producer: class-string<AbstractFactory>,
 *     consumer: class-string<AbstractFactory>,
 *     rpc_server: class-string<AbstractFactory>,
 *     rpc_client: class-string<AbstractFactory>,
 *   },
 *   rabbitmq: array{
 *     connection: array<string, ConnectionOptions>,
 *     producer: array<string, ProducerOptions>,
 *     consumer: array<string, ConsumerOptions>,
 *     rpc_client: array<string, RpcClientOptions>,
 *     rpc_server: array<string, RpcServerOptions>,
 *   },
 *   dependencies: array<string, mixed>,
 * }
 */
final class ConfigProvider
{
    /**
     * @psalm-return ConfigArray
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
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
                'connection' => Service\ConnectionFactory::class,
                'producer' => Service\ProducerFactory::class,
                'consumer' => Service\ConsumerFactory::class,
                'rpc_server' => Service\RpcServerFactory::class,
                'rpc_client' => Service\RpcClientFactory::class,
            ],
            'laminas-cli' => [
                'commands' => [
                    Command\ListConsumersCommand::NAME => Command\ListConsumersCommand::class,
                    Command\StartConsumerCommand::NAME => Command\StartConsumerCommand::class,
                    Command\StartRpcServerCommand::NAME => Command\StartRpcServerCommand::class,
                    Command\PublishMessageCommand::NAME => Command\PublishMessageCommand::class,
                    Command\SetupFabricCommand::NAME => Command\SetupFabricCommand::class,
                ]
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getDependencies(): array
    {
        return [
            'abstract_factories' => [
                Service\AbstractServiceFactory::class => Service\AbstractServiceFactory::class,
            ],
            'factories' => [
                Service\Connection\StreamConnectionFactory::class => InvokableFactory::class,
                Service\Connection\SSLConnectionFactory::class => InvokableFactory::class,
                Service\Connection\SocketConnectionFactory::class => InvokableFactory::class,
                Service\Connection\LazyConnectionFactory::class => InvokableFactory::class,
                Command\ListConsumersCommand::class => Command\Factory\ListConsumersCommandFactory::class,
                Command\StartConsumerCommand::class => Command\Factory\StartConsumerCommandFactory::class,
                Command\StartRpcServerCommand::class => Command\Factory\StartRpcServerCommandFactory::class,
                Command\PublishMessageCommand::class => Command\Factory\PublishMessageCommandFactory::class,
                Command\SetupFabricCommand::class => Command\Factory\SetupFabricCommandFactory::class,
            ],
        ];
    }
}
