<?php

namespace RabbitMqModule\Controller;

use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class ConsumerControllerTest extends AbstractConsoleControllerTestCase
{

    protected function setUp()
    {
        $config = include __DIR__.'/../../TestConfiguration.php.dist';
        $this->setApplicationConfig($config);
        parent::setUp();
    }

    public function testDispatchWithTestConsumer()
    {
        $consumer = static::getMockBuilder('RabbitMqModule\Consumer')
            ->setMethods(['consume'])
            ->disableOriginalConstructor()
            ->getMock();

        $consumer
            ->expects(static::once())
            ->method('consume');

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('rabbitmq.consumer.foo', $consumer);

        ob_start();
        $this->dispatch('rabbitmq consumer foo');
        ob_end_clean();

        $this->assertResponseStatusCode(0);
    }

    public function testDispatchWithInvalidTestConsumer()
    {
        ob_start();
        $this->dispatch('rabbitmq consumer foo');
        $output = ob_get_clean();

        static::assertRegExp('/No consumer with name "foo" found/', $output);

        $this->assertResponseStatusCode(1);
    }

    public function testStopConsumerController()
    {
        $consumer = static::getMockBuilder('RabbitMqModule\Consumer')
            ->setMethods(['forceStopConsumer', 'stopConsuming'])
            ->disableOriginalConstructor()
            ->getMock();

        $consumer->expects(static::once())
            ->method('forceStopConsumer');

        $consumer->expects(static::once())
            ->method('stopConsuming');

        $container = $this->getMockBuilder('Zend\ServiceManager\ServiceLocatorInterface')
            ->getMock();

        $stub = $this->getMockBuilder('RabbitMqModule\\Controller\\ConsumerController')
            ->setConstructorArgs([$container])
            ->setMethods(array('callExit'))
            ->getMock();

        $stub->expects(static::once())
            ->method('callExit');

        /** @var \RabbitMqModule\Consumer $consumer */
        /** @var ConsumerController $controller */
        $controller = $stub;
        $controller->setConsumer($consumer);

        $controller->stopConsumer();
    }

    public function testDispatchWithoutSignals()
    {
        $consumer = static::getMockBuilder('RabbitMqModule\Consumer')
            ->setMethods(['consume'])
            ->disableOriginalConstructor()
            ->getMock();

        $consumer
            ->expects(static::once())
            ->method('consume');

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('rabbitmq.consumer.foo', $consumer);

        ob_start();
        $this->dispatch('rabbitmq consumer foo --without-signals');
        ob_end_clean();

        static::assertTrue(defined('AMQP_WITHOUT_SIGNALS'));

        $this->assertResponseStatusCode(0);
    }

    public function testListConsumersWithNoConsumers()
    {
        ob_start();
        $this->dispatch('rabbitmq list consumers');
        ob_end_clean();

        $this->assertConsoleOutputContains('No consumers defined!');

        $this->assertResponseStatusCode(0);
    }

    public function testListConsumersWithNoConfigKey()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        /** @var array $configuration */
        $configuration = $serviceManager->get('Configuration');
        unset($configuration['rabbitmq']);
        $serviceManager->setService('Configuration', $configuration);

        ob_start();
        $this->dispatch('rabbitmq list consumers');
        ob_end_clean();

        $this->assertConsoleOutputContains('No \'rabbitmq.consumer\' configuration key found!');

        $this->assertResponseStatusCode(0);
    }

    public function testListConsumers()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        /** @var array $configuration */
        $configuration = $serviceManager->get('Configuration');
        $configuration['rabbitmq']['consumer'] = [
            'consumer_key1' => [],
            'consumer_key2' => ['description' => 'foo description']
        ];
        $serviceManager->setService('Configuration', $configuration);

        ob_start();
        $this->dispatch('rabbitmq list consumers');
        $content = ob_get_contents();
        ob_end_clean();


        static::assertTrue(false !== strpos($content, 'consumer_key1'));
        static::assertTrue(false !== strpos($content, 'consumer_key2'));
        static::assertTrue(false !== strpos($content, 'foo description'));

        $this->assertResponseStatusCode(0);
    }
}
