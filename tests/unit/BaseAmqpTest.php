<?php

namespace RabbitMqModule;

class BaseAmqpTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $baseAmqp = static::getMockBuilder('RabbitMqModule\\BaseAmqp')
            ->setConstructorArgs([$connection])
            ->setMethods(['__destruct'])
            ->getMock();

        $baseAmqp->method('__destruct');

        $connection->expects(static::once())->method('channel')->willReturn($channel);

        /** @var \RabbitMqModule\BaseAmqp $baseAmqp */
        static::assertEquals($channel, $baseAmqp->getChannel());
    }

    public function testSetChannel()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $baseAmqp = static::getMockBuilder('RabbitMqModule\\BaseAmqp')
            ->setConstructorArgs([$connection])
            ->setMethods(['__destruct'])
            ->getMock();

        $baseAmqp->method('__destruct');

        /* @var \RabbitMqModule\BaseAmqp $baseAmqp */
        $baseAmqp->setChannel($channel);
        static::assertEquals($channel, $baseAmqp->getChannel());
    }

    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('RabbitMqModule\\BaseAmqp');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    public function testDestruct()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $baseAmqp = static::getMockBuilder('RabbitMqModule\\BaseAmqp')
            ->setConstructorArgs([$connection])
            ->setMethods(null)
            ->getMock();

        $connection->expects(static::once())->method('isConnected')->willReturn(true);
        $connection->expects(static::once())->method('close');

        $channel->expects(static::once())->method('close');

        /* @var \RabbitMqModule\BaseAmqp $baseAmqp */
        $baseAmqp->setChannel($channel);
        $baseAmqp->__destruct();
    }

    public function testReconnect()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $baseAmqp = static::getMockBuilder('RabbitMqModule\\BaseAmqp')
            ->setConstructorArgs([$connection])
            ->setMethods(['__destruct'])
            ->getMock();

        $connection->expects(static::once())->method('isConnected')->willReturn(true);
        $connection->expects(static::once())->method('reconnect');

        /** @var \RabbitMqModule\BaseAmqp $baseAmqp */
        static::assertEquals($baseAmqp, $baseAmqp->reconnect());
    }

    public function testReconnectWhenConnected()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $baseAmqp = static::getMockBuilder('RabbitMqModule\\BaseAmqp')
            ->setConstructorArgs([$connection])
            ->setMethods(['__destruct'])
            ->getMock();

        $connection->expects(static::once())->method('isConnected')->willReturn(false);
        $connection->expects(static::never())->method('reconnect');

        /** @var \RabbitMqModule\BaseAmqp $baseAmqp */
        static::assertEquals($baseAmqp, $baseAmqp->reconnect());
    }
}
