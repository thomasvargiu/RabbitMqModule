<?php

namespace RabbitMqModuleTest;

use Mockery as m;
use RabbitMqModule\Options\Exchange;
use RabbitMqModule\Options\Queue;

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

    public function testExplicitSetupFabric()
    {
        $connection = m::mock('PhpAmqpLib\\Connection\\AbstractConnection');
        $channel = m::mock('PhpAmqpLib\\Channel\\AMQPChannel');
        $baseAmqp = m::mock('RabbitMqModule\\BaseAmqp[__destruct]', [$connection, $channel]);

        $baseAmqp->shouldReceive('__destruct');

        $exchangeOptions = new Exchange([
            'name' => 'test-name',
            'type' => 'test-type'
        ]);

        $queueOptions = new Queue([
            'name' => 'test-name',
            'routing_keys' => [
                'routing1',
                'routing2'
            ]
        ]);

        $channel->shouldReceive('exchange_declare')->once();
        $channel->shouldReceive('queue_declare')->once()
            ->andReturn([$queueOptions->getName()]);
        $channel->shouldReceive('queue_bind')->once()
            ->withArgs([$queueOptions->getName(), $exchangeOptions->getName(), 'routing1']);
        $channel->shouldReceive('queue_bind')->once()
            ->withArgs([$queueOptions->getName(), $exchangeOptions->getName(), 'routing2']);

        $foo = self::getMethod('explicitSetupFabric');
        $foo->invokeArgs($baseAmqp, [$exchangeOptions, $queueOptions]);
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
