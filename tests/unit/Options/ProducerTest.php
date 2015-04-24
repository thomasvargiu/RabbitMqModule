<?php

namespace RabbitMqModuleTest\Options;

use RabbitMqModule\Options\Producer;

class ProducerTest extends \PHPUnit_Framework_TestCase
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
            'class' => 'class-name',
            'auto_setup_fabric_enabled' => false
        ];
        $options = new Producer();
        $options->setFromArray($configuration);

        static::assertEquals($configuration['connection'], $options->getConnection());
        static::assertInstanceOf('RabbitMqModule\\Options\\Exchange', $options->getExchange());
        static::assertInstanceOf('RabbitMqModule\\Options\\Queue', $options->getQueue());
        static::assertEquals($configuration['class'], $options->getClass());
        static::assertFalse($options->isAutoSetupFabricEnabled());
    }
}
