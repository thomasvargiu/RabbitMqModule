<?php

namespace RabbitMqModule\Options;

class ExchangeBindTest extends \PHPUnit\Framework\TestCase
{
    public function testOptions()
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetExchangeInvalidValue()
    {
        $options = new ExchangeBind();
        $options->setExchange('');
    }
}
