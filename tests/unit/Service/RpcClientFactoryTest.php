<?php

namespace RabbitMqModule\Service;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Serializer;
use PhpAmqpLib\Connection\AbstractConnection;
use Psr\Container\ContainerInterface;
use RabbitMqModule\RpcClient;

class RpcClientFactoryTest extends \RabbitMqModule\TestCase
{
    public function testCreateService(): void
    {
        $factory = new RpcClientFactory('foo');

        $connection = $this->prophesize(AbstractConnection::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn([
            'rabbitmq' => [
                'rpc_client' => [
                    'foo' => [
                        'connection' => 'foo',
                        'serializer' => 'PhpSerialize',
                    ],
                ],
            ],
        ]);
        $container->get('rabbitmq.connection.foo')->willReturn($connection->reveal());

        $serializer = Serializer::factory('PhpSerialize');
        $container->get('PhpSerialize')->willReturn($serializer);

        $service = $factory($container->reveal());

        static::assertInstanceOf(RpcClient::class, $service);
        static::assertSame($serializer, $service->getSerializer());
    }

    public function testCreateServiceWithSerializerConfig(): void
    {
        $factory = new RpcClientFactory('foo');

        $connection = $this->prophesize(AbstractConnection::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn([
            'rabbitmq' => [
                'rpc_client' => [
                    'foo' => [
                        'connection' => 'foo',
                        'serializer' => [
                            'name' => 'PhpSerialize',
                        ],
                    ],
                ],
            ],
        ]);
        $container->get('rabbitmq.connection.foo')->willReturn($connection->reveal());

        $container->get('PhpSerialize')->shouldNotBeCalled();

        $service = $factory($container->reveal());

        static::assertInstanceOf(RpcClient::class, $service);
        static::assertInstanceOf(AdapterInterface::class, $service->getSerializer());
    }
}
