<?php

namespace RabbitMqModule\Service;

use PhpAmqpLib\Connection\AbstractConnection;
use Prophecy\Argument;
use RabbitMqModule\Service\Connection\ConnectionFactoryInterface;
use Laminas\ServiceManager\ServiceManager;

class ConnectionFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateService()
    {
        $factory = new ConnectionFactory('foo');

        $factory->setFactoryMap(['foo' => 'FooConnectionFactory']);

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
        $container->get('FooConnectionFactory')->willReturn($connectionFactory->reveal());

        $connectionFactory->createConnection(Argument::type(\RabbitMqModule\Options\Connection::class))
            ->shouldBeCalled()
            ->willReturn($connection->reveal());

        $service = $factory($container->reveal(), 'service-name');

        static::assertSame($connection->reveal(), $service);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateServiceWithInvalidType()
    {
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

        $factory($serviceManager, 'service-name');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateServiceWithInvalidFactory()
    {
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

        $service = $factory($serviceManager, 'service-name');

        static::assertEquals('foo', $service);
    }
}
