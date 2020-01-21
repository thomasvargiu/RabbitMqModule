<?php

namespace RabbitMqModule\Service;

use Laminas\ServiceManager\ServiceManager;

class RpcServerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateService()
    {
        $factory = new RpcServerFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'config',
            [
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
            ]
        );

        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->setMethods(['channel'])
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $callback = $this->getMockBuilder('RabbitMqModule\\ConsumerInterface')
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

        $service = $factory($serviceManager, 'service-name');

        static::assertInstanceOf('RabbitMqModule\\RpcServer', $service);
        static::assertInstanceOf('RabbitMqModule\\Options\\Queue', $service->getQueueOptions());
        static::assertInstanceOf('RabbitMqModule\\Options\\Exchange', $service->getExchangeOptions());
        static::assertNotEmpty($service->getConsumerTag());
        static::assertTrue(is_callable($service->getCallback()));
        static::assertEquals(5, $service->getIdleTimeout());
        static::assertInstanceOf('Laminas\\Serializer\\Adapter\\AdapterInterface', $service->getSerializer());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateServiceWithInvalidCallback()
    {
        $factory = new RpcServerFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'config',
            [
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
                            'idle_timeout' => 5,
                        ],
                    ],
                ],
            ]
        );

        $factory($serviceManager, 'service-name');
    }
}
