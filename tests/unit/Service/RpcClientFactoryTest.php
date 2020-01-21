<?php

namespace RabbitMqModule\Service;

use Laminas\ServiceManager\ServiceManager;

class RpcClientFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateService(): void
    {
        $factory = new RpcClientFactory('foo');
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'config',
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

        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $serviceManager->setService('rabbitmq.connection.foo', $connection);

        $service = $factory($serviceManager);

        static::assertInstanceOf('RabbitMqModule\\RpcClient', $service);
        static::assertInstanceOf('Laminas\\Serializer\\Adapter\\AdapterInterface', $service->getSerializer());
    }
}
