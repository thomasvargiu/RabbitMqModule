<?php

namespace RabbitMqModule\Service;

use InvalidArgumentException;
use Laminas\ServiceManager\ServiceManager;
use PhpAmqpLib\Connection\AbstractConnection;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use RabbitMqModule\Service\Connection\ConnectionFactoryInterface;
use RabbitMqModule\Service\Connection\LazyConnectionFactory;
use RabbitMqModule\TestCase;
use RuntimeException;

class ConnectionFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateService(): void
    {
        $factory = new ConnectionFactory('foo');

        $factory->setFactoryMap(['foo' => LazyConnectionFactory::class]);

        $config = [
            'rabbitmq' => [
                'connection' => [
                    'foo' => [
                        'type' => 'foo',
                    ],
                ],
            ],
        ];

        $container = $this->prophesize(\Laminas\ServiceManager\ServiceManager::class);
        $connection = $this->prophesize(AbstractConnection::class);
        $connectionFactory = $this->prophesize(ConnectionFactoryInterface::class);

        $container->get('config')->willReturn($config);
        $container->get(LazyConnectionFactory::class)->willReturn($connectionFactory->reveal());

        $connectionFactory->createConnection(Argument::type(\RabbitMqModule\Options\Connection::class))
            ->shouldBeCalled()
            ->willReturn($connection->reveal());

        $service = $factory($container->reveal());

        static::assertSame($connection->reveal(), $service);
    }

    public function testCreateServiceWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $factory = new ConnectionFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'config',
            [
                'rabbitmq' => [
                    'connection' => [
                        'foo' => [
                            'type' => 'foo',
                        ],
                    ],
                ],
            ]
        );

        $factory($serviceManager);
    }

    public function testCreateServiceWithInvalidFactory(): void
    {
        $this->expectException(RuntimeException::class);
        $factory = new ConnectionFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'config',
            [
                'rabbitmq' => [
                    'connection' => [
                        'foo' => [
                            'type' => 'bar',
                        ],
                    ],
                ],
            ]
        );

        $serviceManager->setService('barFactoryMock', 'string');

        $factory->setFactoryMap([
            'bar' => 'barFactoryMock',
        ]);

        $service = $factory($serviceManager);

        static::assertEquals('foo', $service);
    }
}
