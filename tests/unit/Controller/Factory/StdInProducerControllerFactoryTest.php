<?php

namespace RabbitMqModule\Controller\Factory;

class StdInProducerControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceLocator = static::getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')
            ->getMock();
        $pluginManager = static::getMockBuilder('Zend\ServiceManager\AbstractPluginManager')
            ->disableOriginalConstructor()
            ->getMock();

        $pluginManager->method('getServiceLocator')->willReturn($serviceLocator);

        $factory = new StdInProducerControllerFactory();
        $controller = $factory->createService($pluginManager);

        static::assertInstanceOf('RabbitMqModule\Controller\StdInProducerController', $controller);
    }
}
