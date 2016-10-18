<?php

namespace RabbitMqModule\Controller\Factory;

class StdInProducerControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceLocator = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')
            ->getMock();
        $pluginManager = $this->getMockBuilder('Zend\ServiceManager\AbstractPluginManager')
            ->disableOriginalConstructor()
            ->getMock();

        $pluginManager->method('getServiceLocator')->willReturn($serviceLocator);

        $factory = new StdInProducerControllerFactory();
        $controller = $factory->createService($pluginManager);

        static::assertInstanceOf('RabbitMqModule\Controller\StdInProducerController', $controller);
    }
}
