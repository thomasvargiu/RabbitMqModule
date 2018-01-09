<?php

namespace RabbitMqModule\Options;

class ProducerTest extends \PHPUnit\Framework\TestCase
{
    public function testOptions()
    {
        $configuration = [
            'connection' => 'connection-name',
            'exchange' => [
                'name' => 'exchange-name',
            ],
            'queue' => [
                'name' => 'queue-name',
            ],
            'class' => 'class-name',
            'auto_setup_fabric_enabled' => false,
        ];
        $options = new Producer();
        $options->setFromArray($configuration);

        static::assertEquals($configuration['connection'], $options->getConnection());
        static::assertInstanceOf('RabbitMqModule\\Options\\Exchange', $options->getExchange());
        static::assertInstanceOf('RabbitMqModule\\Options\\Queue', $options->getQueue());
        static::assertEquals($configuration['class'], $options->getClass());
        static::assertFalse($options->isAutoSetupFabricEnabled());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetQueueInvalidValue()
    {
        $options = new Producer();
        $options->setQueue('');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetExchangeInvalidValue()
    {
        $options = new Producer();
        $options->setExchange('');
    }
}
