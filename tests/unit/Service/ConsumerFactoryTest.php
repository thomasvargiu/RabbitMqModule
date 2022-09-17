<?php

namespace RabbitMqModule\Service;

use InvalidArgumentException;
use Laminas\ServiceManager\ServiceManager;
use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\Consumer;
use RabbitMqModule\ConsumerInterface;
use RabbitMqModule\Options;

class ConsumerFactoryTest extends \RabbitMqModule\TestCase
{
    public function testCreateService(): void
    {
        $factory = new ConsumerFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'config',
            [
                'rabbitmq' => [
                    'consumer' => [
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
                        ],
                    ],
                ],
            ]
        );

        $connection = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->setMethods(['channel'])
            ->getMockForAbstractClass();
        $callback = $this->getMockBuilder(ConsumerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMockForAbstractClass();
        $connection->expects(static::never())
            ->method('channel');
        $serviceManager->setService('rabbitmq.connection.foo', $connection);
        $serviceManager->setService('callback-service', $callback);

        $service = $factory($serviceManager);

        static::assertInstanceOf(Consumer::class, $service);
        static::assertInstanceOf(Options\Queue::class, $service->getQueueOptions());
        static::assertInstanceOf(Options\Exchange::class, $service->getExchangeOptions());
        static::assertNotEmpty($service->getConsumerTag());
        static::assertIsCallable($service->getCallback());
        static::assertEquals(5, $service->getIdleTimeout());
    }

    public function testCreateServiceWithInvalidCallback(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $factory = new ConsumerFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'config',
            [
                'rabbitmq' => [
                    'consumer' => [
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
                            'idle_timeout' => 5,
                        ],
                    ],
                ],
            ]
        );

        $factory($serviceManager);
    }
}
