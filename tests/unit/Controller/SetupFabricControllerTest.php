<?php

namespace RabbitMqModule\Controller;

use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class SetupFabricControllerTest extends AbstractConsoleControllerTestCase
{
    protected function setUp()
    {
        $config = include __DIR__.'/../../TestConfiguration.php';
        $this->setApplicationConfig($config);
        parent::setUp();
    }

    public function testDispatch()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        $service = $this->getMockBuilder('RabbitMqModule\\Service\\SetupFabricAwareInterface')
            ->getMockForAbstractClass();
        $service->expects(static::exactly(4))
            ->method('setupFabric');
        $someOtherService = new \ArrayObject();
        $serviceManager->setService('rabbitmq.consumer.foo-consumer1', $service);
        $serviceManager->setService('rabbitmq.consumer.foo-consumer2', $service);
        $serviceManager->setService('rabbitmq.producer.bar-producer1', $service);
        $serviceManager->setService('rabbitmq.producer.bar-producer2', $service);
        $serviceManager->setService('rabbitmq.producer.bar-producer-fake', $someOtherService);

        /** @var array $configuration */
        $configuration = $serviceManager->get('Configuration');
        $configuration['rabbitmq']['consumer'] = [
            'foo-consumer1' => [],
            'foo-consumer2' => [],
        ];
        $configuration['rabbitmq']['producer'] = [
            'bar-producer1' => [],
            'bar-producer2' => [],
            'bar-producer-fake' => [],
        ];
        $serviceManager->setService('Configuration', $configuration);

        ob_start();
        $this->dispatch('rabbitmq setup-fabric');
        ob_end_clean();

        $this->assertResponseStatusCode(0);
    }

    public function testDispatchWithInvalidConfigKeys()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);

        /** @var array $configuration */
        $configuration = $serviceManager->get('Configuration');
        $configuration['rabbitmq'] = null;
        $serviceManager->setService('Configuration', $configuration);

        ob_start();
        $this->dispatch('rabbitmq setup-fabric');
        ob_end_clean();

        $this->assertResponseStatusCode(1);
    }
}
