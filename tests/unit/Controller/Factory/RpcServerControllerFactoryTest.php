<?php

namespace RabbitMqModule\Controller\Factory;

class RpcServerControllerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $container = $this->getMockBuilder('Interop\Container\ContainerInterface')
            ->getMock();

        $factory = new RpcServerControllerFactory();
        $controller = $factory($container, 'service-name');

        static::assertInstanceOf('RabbitMqModule\Controller\RpcServerController', $controller);
    }
}
