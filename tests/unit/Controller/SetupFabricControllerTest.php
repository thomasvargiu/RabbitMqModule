<?php

namespace RabbitMqModule\Controller;

use Zend\ServiceManager\ServiceManager;

class SetupFabricControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SetupFabricController
     */
    protected $controller;

    public function setUp()
    {
        $this->controller = new SetupFabricController();
        $console = static::getMockBuilder('Zend\\Console\\Adapter\\AdapterInterface')
            ->getMockForAbstractClass();
        $console->expects(static::any())
            ->method('writeLine');
        $console->expects(static::any())
            ->method('write');
        /** @var \Zend\Console\Adapter\AdapterInterface $console */
        $this->controller->setConsole($console);
    }

    public function testAction()
    {
        $configuration = [
            'rabbitmq' => [
                'consumer' => [
                    'foo-consumer1' => [],
                    'foo-consumer2' => []
                ],
                'producer' => [
                    'bar-producer1' => [],
                    'bar-producer2' => []
                ]
            ]
        ];
        $serviceLocator = new ServiceManager();
        $service = static::getMockBuilder('RabbitMqModule\\Service\\SetupFabricAwareInterface')
            ->getMockForAbstractClass();
        $service->expects(static::exactly(4))
            ->method('setupFabric');

        $serviceLocator->setService('Configuration', $configuration);
        $serviceLocator->setService('rabbitmq.consumer.foo-consumer1', $service);
        $serviceLocator->setService('rabbitmq.consumer.foo-consumer2', $service);
        $serviceLocator->setService('rabbitmq.producer.bar-producer1', $service);
        $serviceLocator->setService('rabbitmq.producer.bar-producer2', $service);

        $this->controller->setServiceLocator($serviceLocator);
        $this->controller->indexAction();
    }
}
