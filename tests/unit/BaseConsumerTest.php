<?php

namespace RabbitMqModuleTest;

use Mockery as m;

class BaseConsumerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetOptions()
    {
        $connection = m::mock('PhpAmqpLib\\Connection\\AbstractConnection');
        $options = m::mock('RabbitMqModule\\Options\\Consumer[]');
        $baseConsumer = m::mock('RabbitMqModule\\BaseConsumer[__destruct]', [$connection]);

        $baseConsumer->shouldReceive('__destruct');

        /** @var \RabbitMqModule\BaseConsumer $baseConsumer */
        $baseConsumer->setOptions($options);
        static::assertEquals($options, $baseConsumer->getOptions());
    }
}
