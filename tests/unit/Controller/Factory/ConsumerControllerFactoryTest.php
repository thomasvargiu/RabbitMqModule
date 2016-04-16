<?php

namespace RabbitMqModule\Controller\Factory;

class ConsumerControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceLocator = static::getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')
            ->getMock();
        $pluginManager = static::getMockBuilder('Zend\ServiceManager\AbstractPluginManager')
            ->disableOriginalConstructor()
            ->getMock();

        $pluginManager->method('getServiceLocator')->willReturn($serviceLocator);

        $factory = new ConsumerControllerFactory();
        $controller = $factory->createService($pluginManager);

        static::assertInstanceOf('RabbitMqModule\Controller\ConsumerController', $controller);
    }
}
