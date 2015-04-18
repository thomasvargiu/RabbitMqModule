<?php

namespace RabbitMqModuleTest\Service;

use RabbitMqModule\Service\ConnectionFactory;
use Zend\ServiceManager\ServiceManager;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $factory = new ConnectionFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'Configuration',
            [
                'rabbitmq' => [
                    'connection' => [
                        'foo' => [
                            'type' => 'bar'
                        ]
                    ]
                ]
            ]
        );

        $factoryMock = static::getMockBuilder('RabbitMqModule\\Service\\Connection\\ConnectionFactoryInterface')
            ->getMock();
        $factoryMock->expects(static::once())
            ->method('createConnection')
            ->will(static::returnValue('foo'));

        $serviceManager->setService('barFactoryMock', $factoryMock);

        $factory->setFactoryMap([
            'bar' => 'barFactoryMock'
        ]);

        $service = $factory->createService($serviceManager);

        static::assertEquals('foo', $service);
    }
}
