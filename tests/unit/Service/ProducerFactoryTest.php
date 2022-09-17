<?php

namespace RabbitMqModule\Service;

use Laminas\ServiceManager\ServiceManager;

class ProducerFactoryTest extends \RabbitMqModule\TestCase
{
    public function testCreateService(): void
    {
        $factory = new ProducerFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'config',
            [
                'rabbitmq' => [
                    'producer' => [
                        'foo' => [
                            'connection' => 'foo',
                            'exchange' => [
                                'name' => 'exchange-name',
                            ],
                            'queue' => [
                                'name' => 'queue-name',
                            ],
                            'auto_setup_fabric_enabled' => false,
                        ],
                    ],
                ],
            ]
        );

        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $serviceManager->setService(
            'rabbitmq.connection.foo',
            $connection
        );

        $service = $factory($serviceManager);

        static::assertInstanceOf('RabbitMqModule\\Producer', $service);
        static::assertSame($connection, $service->getConnection());
        static::assertEquals('exchange-name', $service->getExchangeOptions()->getName());
        static::assertEquals('queue-name', $service->getQueueOptions()->getName());
        static::assertFalse($service->isAutoSetupFabricEnabled());
    }
}
