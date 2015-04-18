<?php

namespace RabbitMqModuleTest\Service;

use RabbitMqModule\Service\ProducerFactory;
use Zend\ServiceManager\ServiceManager;

class ProducerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $factory = new ProducerFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'Configuration',
            [
                'rabbitmq' => [
                    'producer' => [
                        'foo' => [
                            'connection' => 'foo'
                        ]
                    ]
                ]
            ]
        );

        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $rabbitMqService = static::getMockBuilder('RabbitMqModule\\Service\\RabbitMqService')
            ->getMock();
        $serviceManager->setService(
            'rabbitmq.connection.foo',
            $connection
        );
        $serviceManager->setService(
            'RabbitMqModule\\Service\\RabbitMqService',
            $rabbitMqService
        );

        $service = $factory->createService($serviceManager);

        static::assertInstanceOf('RabbitMqModule\\Producer', $service);
        static::assertSame($rabbitMqService, $service->getRabbitMqService());
    }
}
