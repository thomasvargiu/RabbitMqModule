<?php

namespace RabbitMqModule\Options;

use InvalidArgumentException;

class RpcClientTest extends \PHPUnit\Framework\TestCase
{
    public function testSetConnection(): void
    {
        $options = new RpcClient();
        $options->setConnection('connection-name');

        static::assertEquals('connection-name', $options->getConnection());
    }

    public function testSetSerializer(): void
    {
        $options = new RpcClient();

        $options->setSerializer('PhpSerialize');
        static::assertInstanceOf('Laminas\\Serializer\\Adapter\\AdapterInterface', $options->getSerializer());

        $options->setSerializer(null);
        static::assertNull($options->getSerializer());

        $options->setSerializer(['name' => 'PhpSerialize']);
        static::assertInstanceOf('Laminas\\Serializer\\Adapter\\AdapterInterface', $options->getSerializer());
    }

    public function testSetSerializerWithEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $options = new RpcClient();

        $options->setSerializer([]);
    }
}
