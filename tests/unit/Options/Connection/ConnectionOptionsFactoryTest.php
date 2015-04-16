<?php

namespace RabbitMqModuleTest\Options\Connection;

use RabbitMqModule\Options\Connection\ConnectionOptionsFactory;
use RabbitMqModule\Service\Connection\ConnectionFactory;

class ConnectionOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryWithStream()
    {
        $type = ConnectionFactory::TYPE_STREAM;
        static::assertEquals('stream', $type);

        $optionsFactory = new ConnectionOptionsFactory();
        $options = $optionsFactory->createOptions($type);
        static::assertInstanceOf('RabbitMqModule\\Options\\Connection\\StreamConnection', $options);
    }

    public function testFactoryWithSocket()
    {
        $type = ConnectionFactory::TYPE_SOCKET;
        static::assertEquals('socket', $type);

        $optionsFactory = new ConnectionOptionsFactory();
        $options = $optionsFactory->createOptions($type);
        static::assertInstanceOf('RabbitMqModule\\Options\\Connection\\SocketConnection', $options);
    }

    public function testFactoryWithSsl()
    {
        $type = ConnectionFactory::TYPE_SSL;
        static::assertEquals('ssl', $type);

        $optionsFactory = new ConnectionOptionsFactory();
        $options = $optionsFactory->createOptions($type);
        static::assertInstanceOf('RabbitMqModule\\Options\\Connection\\SSLConnection', $options);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFactoryWithInvalidType()
    {
        $type = 'unknown';
        $optionsFactory = new ConnectionOptionsFactory();
        $optionsFactory->createOptions($type);
    }
}
