<?php

namespace RabbitMqModule\Options;

use InvalidArgumentException;

class RpcServerTest extends \PHPUnit\Framework\TestCase
{
    public function testSetSerializer(): void
    {
        $options = new RpcServer();

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
        $options = new RpcServer();

        $options->setSerializer([]);
    }
}
