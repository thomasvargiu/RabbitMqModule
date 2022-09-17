<?php

namespace RabbitMqModule\Service;

use InvalidArgumentException;
use Laminas\Serializer\Adapter\AdapterInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use Psr\Container\ContainerInterface;
use RabbitMqModule\Options;
use RabbitMqModule\RpcServer;
use RabbitMqModule\TestCase;

class RpcServerFactoryTest extends TestCase
{
    public function testCreateService(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn([
            'rabbitmq' => [
                'rpc_server' => [
                    'foo' => [
                        'connection' => 'foo',
                        'exchange' => [
                        ],
                        'queue' => [
                            'name' => 'bar',
                        ],
                        'qos' => [
                            'prefetch_size' => 99,
                            'prefetch_count' => 89,
                        ],
                        'callback' => 'callback-service',
                        'idle_timeout' => 5,
                        'serializer' => 'PhpSerialize',
                    ],
                ],
            ],
        ]);

        $connection = $this->prophesize(AbstractConnection::class);
        $callback = new class() {
            public function __invoke()
            {
            }
        };
        $serializer = $this->prophesize(AdapterInterface::class);

        $container->get('rabbitmq.connection.foo')->willReturn($connection->reveal());
        $container->get('callback-service')->willReturn($callback);
        $container->get('PhpSerialize')->willReturn($serializer->reveal());

        $factory = new RpcServerFactory('foo');
        $service = $factory($container->reveal());

        static::assertInstanceOf(RpcServer::class, $service);
        static::assertInstanceOf(Options\Queue::class, $service->getQueueOptions());
        static::assertInstanceOf(Options\Exchange::class, $service->getExchangeOptions());
        static::assertNotEmpty($service->getConsumerTag());
        static::assertIsCallable($service->getCallback());
        static::assertSame(5, $service->getIdleTimeout());
        static::assertSame($serializer->reveal(), $service->getSerializer());
    }

    public function testCreateServiceWithInvalidCallback(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid callback provided');
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn([
            'rabbitmq' => [
                'rpc_server' => [
                    'foo' => [
                        'connection' => 'foo',
                    ],
                ],
            ],
        ]);

        $factory = new RpcServerFactory('foo');
        $factory($container->reveal());
    }
}
