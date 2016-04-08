<?php

namespace RabbitMqModule\Service\Connection;

use RabbitMqModule\Options\Connection;

/**
 * @author Krzysztof Gzocha <krzysztof@propertyfinder.ae>
 * @package RabbitMqModule\Service\Connection
 */
class StreamConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $options = new Connection(['lazy' => true]);
        $factory = new StreamConnectionFactory();

        $result = $factory->createConnection($options);
        $this->assertInstanceOf(
            '\PhpAmqpLib\Connection\AMQPLazyConnection',
            $result
        );
    }
}
