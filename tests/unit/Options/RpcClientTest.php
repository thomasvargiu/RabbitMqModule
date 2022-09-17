<?php

namespace RabbitMqModule\Options;

class RpcClientTest extends \RabbitMqModule\TestCase
{
    public function testSetConnection(): void
    {
        $options = new RpcClient();
        $options->setConnection('connection-name');
        $options->setSerializer('serializer-service');

        static::assertEquals('connection-name', $options->getConnection());
    }
}
