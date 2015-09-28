<?php

namespace RabbitMqModuleTest\Options;

use RabbitMqModule\Options\RpcServer;

class RpcServerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetSerializer()
    {
        $options = new RpcServer();

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
        $options = new RpcServer();

        $options->setSerializer(true);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetSerializerWithEmptyArray()
    {
        $options = new RpcServer();

        $options->setSerializer([]);
    }
}
