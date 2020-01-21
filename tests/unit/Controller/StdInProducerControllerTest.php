<?php

namespace RabbitMqModule\Controller;

use Laminas\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class StdInProducerControllerTest extends AbstractConsoleControllerTestCase
{
    protected function setUp()
    {
        $config = include __DIR__ . '/../../TestConfiguration.php';
        $this->setApplicationConfig($config);
        parent::setUp();
    }

    public function testDispatchWithTestProducer()
    {
        $producer = $this->getMockBuilder('RabbitMqModule\Producer')
            ->setMethods(['publish'])
            ->disableOriginalConstructor()
            ->getMock();
        $producer
            ->expects(static::once())
            ->method('publish')
            ->with(
                static::equalTo('msg'),
                static::equalTo('bar')
            );

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('rabbitmq.producer.foo', $producer);

        ob_start();
        $this->dispatch('rabbitmq stdin-producer foo --route=bar msg');
        ob_end_clean();

        $this->assertResponseStatusCode(0);
    }

    public function testDispatchWithInvalidTestProducer()
    {
        ob_start();
        $this->dispatch('rabbitmq stdin-producer foo --route=bar msg');
        $output = ob_get_clean();

        static::assertRegExp('/No producer with name "foo" found/', $output);

        $this->assertResponseStatusCode(1);
    }
}
