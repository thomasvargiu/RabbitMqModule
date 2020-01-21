<?php

namespace RabbitMqModule\Controller;

use Laminas\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class RpcServerControllerTest extends AbstractConsoleControllerTestCase
{
    protected function setUp()
    {
        $config = include __DIR__ . '/../../TestConfiguration.php';
        $this->setApplicationConfig($config);
        parent::setUp();
    }

    public function testDispatchWithTestConsumer()
    {
        $consumer = $this->getMockBuilder('RabbitMqModule\Consumer')
            ->setMethods(['consume'])
            ->disableOriginalConstructor()
            ->getMock();
        $consumer
            ->expects(static::once())
            ->method('consume');

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('rabbitmq.rpc_server.foo', $consumer);

        ob_start();
        $this->dispatch('rabbitmq rpc_server foo');
        ob_end_clean();

        $this->assertResponseStatusCode(0);
    }

    public function testDispatchWithInvalidTestConsumer()
    {
        ob_start();
        $this->dispatch('rabbitmq rpc_server foo');
        $output = ob_get_clean();

        static::assertRegExp('/No rpc server with name "foo" found/', $output);

        $this->assertResponseStatusCode(1);
    }
}
