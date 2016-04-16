<?php

namespace RabbitMqModule\Controller\Factory;

class SetupFabricControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceLocator = static::getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')
            ->getMock();
        $pluginManager = static::getMockBuilder('Zend\ServiceManager\AbstractPluginManager')
            ->disableOriginalConstructor()
            ->getMock();

        $pluginManager->method('getServiceLocator')->willReturn($serviceLocator);

        $factory = new SetupFabricControllerFactory();
        $controller = $factory->createService($pluginManager);

        static::assertInstanceOf('RabbitMqModule\Controller\SetupFabricController', $controller);
    }
}
