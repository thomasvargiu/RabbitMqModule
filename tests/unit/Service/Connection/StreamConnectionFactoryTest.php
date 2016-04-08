<?php

namespace RabbitMqModule\Service\Connection;
use RabbitMqModule\Options\Connection;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package RabbitMqModule\Service\Connection
 */
class StreamConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param $lazy
     * @param $expectedConnectionClass
     * @dataProvider createServiceDataProvider
     */
    public function testCreateService($lazy, $expectedConnectionClass)
    {
        $options = new Connection(['lazy' => $lazy]);
        $factory = new StreamConnectionFactory();

        $result = $factory->createConnection($options);
        $this->assertInstanceOf(
            $expectedConnectionClass,
            $result
        );
    }

    /**
     * @return array
     */
    public function createServiceDataProvider()
    {
        return [
            [true, '\PhpAmqpLib\Connection\AMQPLazyConnection'],
            [true, '\PhpAmqpLib\Connection\AMQPStreamConnection'],
        ];
    }
}
