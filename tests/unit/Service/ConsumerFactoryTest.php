<?php

namespace RabbitMqModule\Service;

use InvalidArgumentException;
use Laminas\ServiceManager\ServiceManager;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\Consumer;
use RabbitMqModule\ConsumerInterface;
use RabbitMqModule\Options;

class ConsumerFactoryTest extends \PHPUnit\Framework\TestCase
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
        $channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $callback = $this->getMockBuilder(ConsumerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMockForAbstractClass();
        $connection->expects(static::once())
            ->method('channel')
            ->will(static::returnValue($channel));
        $channel->expects(static::once())
            ->method('basic_qos')
            ->with(
                static::equalTo(99),
                static::equalTo(89),
                static::equalTo(false)
            );
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
