<?php

namespace RabbitMqModuleTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class SetupFabricControllerTest extends AbstractConsoleControllerTestCase
{

    protected function setUp()
    {
        $config = include __DIR__ . '/../../TestConfiguration.php.dist';
        $this->setApplicationConfig($config);
        parent::setUp();
    }

    public function testDispatch()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $service = static::getMockBuilder('RabbitMqModule\\Service\\SetupFabricAwareInterface')
            ->getMockForAbstractClass();
        $service->expects(static::exactly(4))
            ->method('setupFabric');
        $serviceManager->setService('rabbitmq.consumer.foo-consumer1', $service);
        $serviceManager->setService('rabbitmq.consumer.foo-consumer2', $service);
        $serviceManager->setService('rabbitmq.producer.bar-producer1', $service);
        $serviceManager->setService('rabbitmq.producer.bar-producer2', $service);

        $configuration = $serviceManager->get('Configuration');
        $configuration['rabbitmq']['consumer'] = [
            'foo-consumer1' => [],
            'foo-consumer2' => []
        ];
        $configuration['rabbitmq']['producer'] = [
            'bar-producer1' => [],
            'bar-producer2' => []
        ];
        $serviceManager->setService('Configuration', $configuration);

        ob_start();
        $this->dispatch('rabbitmq setup-fabric');
        ob_end_clean();

        $this->assertResponseStatusCode(0);
    }
}
