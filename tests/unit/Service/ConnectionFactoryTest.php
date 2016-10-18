<?php

namespace RabbitMqModule\Service;

use Zend\ServiceManager\ServiceManager;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
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

        $factoryMock = $this->getMockBuilder('RabbitMqModule\\Service\\Connection\\ConnectionFactoryInterface')
            ->getMock();
        $factoryMock->expects(static::once())
            ->method('createConnection')
            ->will(static::returnValue('foo'));

        $serviceManager->setService('barFactoryMock', $factoryMock);

        $factory->setFactoryMap([
            'bar' => 'barFactoryMock',
        ]);

        $service = $factory->createService($serviceManager);

        static::assertEquals('foo', $service);
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

        $factory->createService($serviceManager);
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

        $service = $factory->createService($serviceManager);

        static::assertEquals('foo', $service);
    }
}
