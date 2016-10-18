<?php

namespace RabbitMqModule\Controller\Factory;

class SetupFabricControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceLocator = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')
            ->getMock();
        $pluginManager = $this->getMockBuilder('Zend\ServiceManager\AbstractPluginManager')
            ->disableOriginalConstructor()
            ->getMock();

        $pluginManager->method('getServiceLocator')->willReturn($serviceLocator);

        $factory = new SetupFabricControllerFactory();
        $controller = $factory->createService($pluginManager);

        static::assertInstanceOf('RabbitMqModule\Controller\SetupFabricController', $controller);
    }
}
