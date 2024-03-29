<?php

namespace RabbitMqModule\Options;

use InvalidArgumentException;

class ProducerTest extends \RabbitMqModule\TestCase
{
    public function testOptions(): void
    {
        $configuration = [
            'connection' => 'connection-name',
            'exchange' => [
                'name' => 'exchange-name',
            ],
            'queue' => [
                'name' => 'queue-name',
            ],
            'auto_setup_fabric_enabled' => false,
        ];
        $options = new Producer();
        $options->setFromArray($configuration);

        static::assertEquals($configuration['connection'], $options->getConnection());
        static::assertInstanceOf('RabbitMqModule\\Options\\Exchange', $options->getExchange());
        static::assertInstanceOf('RabbitMqModule\\Options\\Queue', $options->getQueue());
        static::assertFalse($options->isAutoSetupFabricEnabled());
    }

    public function testSetQueueInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $options = new Producer();
        $options->setQueue('');
    }

    public function testSetExchangeInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $options = new Producer();
        $options->setExchange('');
    }
}
