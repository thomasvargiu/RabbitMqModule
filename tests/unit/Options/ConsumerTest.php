<?php

namespace RabbitMqModule\Options;

use InvalidArgumentException;

class ConsumerTest extends \RabbitMqModule\TestCase
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
            'callback' => 'callback-name',
            'idle_timeout' => 6,
            'qos' => [
            ],
            'auto_setup_fabric_enabled' => false,
            'consumer_tag' => 'test-tag',
            'signals_enabled' => true,
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
        static::assertEquals($configuration['auto_setup_fabric_enabled'], $options->isAutoSetupFabricEnabled());
        static::assertEquals('test-tag', $options->getConsumerTag());
        static::assertEquals($configuration['signals_enabled'], $options->isSignalsEnabled());
    }

    public function testSetQueueInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $options = new Consumer();
        $options->setQueue('');
    }

    public function testSetExchangeInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $options = new Consumer();
        $options->setExchange('');
    }

    public function testSetWosInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $options = new Consumer();
        $options->setQos('');
    }
}
