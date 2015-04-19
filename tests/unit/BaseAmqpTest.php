<?php

namespace RabbitMqModuleTest;

use Mockery as m;

class BaseAmqpTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $connection = m::mock('PhpAmqpLib\\Connection\\AbstractConnection');
        $channel = m::mock('PhpAmqpLib\\Channel\\AMQPChannel');
        $baseAmqp = m::mock('RabbitMqModule\\BaseAmqp[__destruct]', [$connection]);

        $baseAmqp->shouldReceive('__destruct');

        $connection->shouldReceive('channel')->once()->andReturn($channel);

        /** @var \RabbitMqModule\BaseAmqp $baseAmqp */
        static::assertEquals($channel, $baseAmqp->getChannel());
    }

    public function testSetChannel()
    {
        $connection = m::mock('PhpAmqpLib\\Connection\\AbstractConnection');
        $channel = m::mock('PhpAmqpLib\\Channel\\AMQPChannel');
        $baseAmqp = m::mock('RabbitMqModule\\BaseAmqp[__destruct]', [$connection]);

        $baseAmqp->shouldReceive('__destruct');

        /** @var \RabbitMqModule\BaseAmqp $baseAmqp */
        $baseAmqp->setChannel($channel);
        static::assertEquals($channel, $baseAmqp->getChannel());
    }

    protected static function getMethod($name) {
        $class = new \ReflectionClass('RabbitMqModule\\BaseAmqp');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    public function testDestruct()
    {
        $connection = m::mock('PhpAmqpLib\\Connection\\AbstractConnection');
        $channel = m::mock('PhpAmqpLib\\Channel\\AMQPChannel');
        $baseAmqp = m::mock('RabbitMqModule\\BaseAmqp[]', [$connection]);

        $connection->shouldReceive('isConnected')->once()->andReturn(true);
        $connection->shouldReceive('close')->once();

        $channel->shouldReceive('close')->once();

        $baseAmqp->shouldReceive('__destruct');

        /** @var \RabbitMqModule\BaseAmqp $baseAmqp */
        $baseAmqp->setChannel($channel);
        $baseAmqp->__destruct();
    }

    public function testReconnect()
    {
        $connection = m::mock('PhpAmqpLib\\Connection\\AbstractConnection');
        $baseAmqp = m::mock('RabbitMqModule\\BaseAmqp[__destruct]', [$connection]);

        $connection->shouldReceive('isConnected')->once()->andReturn(true);
        $connection->shouldReceive('reconnect')->once();

        $baseAmqp->shouldReceive('__destruct');

        /** @var \RabbitMqModule\BaseAmqp $baseAmqp */
        static::assertEquals($baseAmqp, $baseAmqp->reconnect());
    }

    protected function tearDown()
    {
        m::close();
    }
}
