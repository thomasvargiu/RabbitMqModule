<?php

namespace RabbitMqModuleTest\Options;

use RabbitMqModule\Options\RpcClient;

class RpcClientTest extends \PHPUnit_Framework_TestCase
{

    public function testSetConnection()
    {
        $options = new RpcClient();
        $options->setConnection('connection-name');

        static::assertEquals('connection-name', $options->getConnection());
    }

    public function testSetSerializer()
    {
        $options = new RpcClient();

        $options->setSerializer('PhpSerialize');
        static::assertInstanceOf('Zend\\Serializer\\Adapter\\AdapterInterface', $options->getSerializer());

        $options->setSerializer(null);
        static::assertNull($options->getSerializer());

        $options->setSerializer(['name' => 'PhpSerialize']);
        static::assertInstanceOf('Zend\\Serializer\\Adapter\\AdapterInterface', $options->getSerializer());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetSerializerWithInvalidValue()
    {
        $options = new RpcClient();

        $options->setSerializer(true);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetSerializerWithEmptyArray()
    {
        $options = new RpcClient();

        $options->setSerializer([]);
    }
}
