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
                            'connection' => 'foo',
                            'exchange' => [

                            ]
                        ]
                    ]
                ]
            ]
        );

        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $serviceManager->setService(
            'rabbitmq.connection.foo',
            $connection
        );

        $service = $factory->createService($serviceManager);

        static::assertInstanceOf('RabbitMqModule\\Producer', $service);
    }
}
