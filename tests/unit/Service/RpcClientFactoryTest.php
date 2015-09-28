<?php

namespace RabbitMqModuleTest\Service;

use RabbitMqModule\Service\RpcClientFactory;
use Zend\ServiceManager\ServiceManager;

class RpcClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $factory = new RpcClientFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'Configuration',
            [
                'rabbitmq' => [
                    'rpc_client' => [
                        'foo' => [
                            'connection' => 'foo',
                            'serializer' => 'PhpSerialize',
                        ],
                    ],
                ],
            ]
        );

        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $serviceManager->setService('rabbitmq.connection.foo', $connection);

        $service = $factory->createService($serviceManager);

        static::assertInstanceOf('RabbitMqModule\\RpcClient', $service);
        static::assertInstanceOf('Zend\\Serializer\\Adapter\\AdapterInterface', $service->getSerializer());
    }
}
