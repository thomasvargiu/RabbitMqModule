<?php

namespace RabbitMqModuleTest\Controller;

use RabbitMqModule\Controller\ConsumerController;
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
        $consumer = static::getMock('RabbitMqModule\Consumer', array('consume'), array(), '', false);
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
        $consumer = static::getMock('RabbitMqModule\Consumer', ['forceStopConsumer', 'stopConsuming'], [], '', false);

        $consumer->expects(static::once())
            ->method('forceStopConsumer');

        $consumer->expects(static::once())
            ->method('stopConsuming');

        $controller = new ConsumerController();
        $controller->setConsumer($consumer);

        $controller->stopConsumer();
    }
}
