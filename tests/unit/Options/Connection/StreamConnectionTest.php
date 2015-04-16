<?php

namespace RabbitMqModuleTest\Options\Connection;

use RabbitMqModule\Options\Connection\StreamConnection as Options;

class StreamConnectionTest extends \PHPUnit_Framework_TestCase
{

    public function testOptions()
    {
        $options = new Options();
        $options->setHeartbeat(234);
        static::assertEquals(234, $options->getHeartbeat());

        $options->setConnectionTimeout(432);
        static::assertEquals(432, $options->getConnectionTimeout());
    }
}
