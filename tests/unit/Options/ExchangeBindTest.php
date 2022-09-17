<?php

namespace RabbitMqModule\Options;

use InvalidArgumentException;

class ExchangeBindTest extends \RabbitMqModule\TestCase
{
    public function testOptions(): void
    {
        $configuration = [
            'exchange' => ['name' => 'foo'],
            'routing_keys' => ['routing.1', 'routing.2'],
        ];
        $options = new ExchangeBind();
        $options->setFromArray($configuration);

        static::assertInstanceOf('RabbitMqModule\\Options\\Exchange', $options->getExchange());
        static::assertCount(2, $options->getRoutingKeys());
        static::assertEquals(['routing.1', 'routing.2'], $options->getRoutingKeys());
    }

    public function testSetExchangeInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $options = new ExchangeBind();
        $options->setExchange('');
    }
}
