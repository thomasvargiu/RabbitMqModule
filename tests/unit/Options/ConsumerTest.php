<?php

namespace RabbitMqModuleTest\Options;

use RabbitMqModule\Options\Consumer;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{

    public function testOptions()
    {
        $configuration = [
            'connection' => 'connection-name',
            'exchange' => [
                'name' => 'exchange-name'
            ],
            'queue' => [
                'name' => 'queue-name'
            ],
            'callback' => 'callback-name',
            'idle_timeout' => 6,
            'qos' => [

            ],
            'auto_setup_fabric_enabled' => false,
            'consumer_tag' => 'test-tag'
        ];
        $options = new Consumer();
        $options->setFromArray($configuration);

        static::assertEquals($configuration['connection'], $options->getConnection());
        static::assertInstanceOf('RabbitMqModule\\Options\\Exchange', $options->getExchange());
        static::assertInstanceOf('RabbitMqModule\\Options\\Queue', $options->getQueue());
        static::assertEquals($configuration['callback'], $options->getCallback());
        static::assertEquals($configuration['idle_timeout'], $options->getIdleTimeout());
        static::assertInstanceOf('RabbitMqModule\\Options\\Qos', $options->getQos());
        static::assertEquals(6, $options->getIdleTimeout());
        static::assertFalse($options->isAutoSetupFabricEnabled());
        static::assertSame('test-tag', $options->getConsumerTag());
    }
}
