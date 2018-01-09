<?php

namespace RabbitMqModule\Controller\Factory;

class SetupFabricControllerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $container = $this->getMockBuilder('Interop\Container\ContainerInterface')
            ->getMock();

        $factory = new SetupFabricControllerFactory();
        $controller = $factory($container, 'service-name');

        static::assertInstanceOf('RabbitMqModule\Controller\SetupFabricController', $controller);
    }
}
