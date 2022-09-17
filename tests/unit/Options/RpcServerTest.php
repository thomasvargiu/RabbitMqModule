<?php

namespace RabbitMqModule\Options;

class RpcServerTest extends \RabbitMqModule\TestCase
{
    public function testSetSerializer(): void
    {
        $options = new RpcServer();

        $options->setSerializer('PhpSerialize');
        static::assertSame('PhpSerialize', $options->getSerializer());

        $options->setSerializer(null);
        static::assertNull($options->getSerializer());

        $options->setSerializer(['name' => 'PhpSerialize']);
        static::assertSame(['name' => 'PhpSerialize'], $options->getSerializer());
    }
}
