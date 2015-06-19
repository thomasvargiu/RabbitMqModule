<?php

namespace RabbitMqModuleTest\Options;

use RabbitMqModule\Options\ExchangeBind;

class ExchangeBindTest extends \PHPUnit_Framework_TestCase
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
}
