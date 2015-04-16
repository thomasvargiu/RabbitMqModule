<?php

namespace RabbitMqModuleTest\Options\Connection;

use RabbitMqModule\Options\Connection\SSLConnection as SSLConnectionOptions;

class SSLConnectionTest extends \PHPUnit_Framework_TestCase
{

    public function testSslOptions()
    {
        $sslOptions = [
            'opt1' => 'value1',
            'opt2' => 'value2'
        ];
        $options = new SSLConnectionOptions;
        $options->setSslOptions($sslOptions);

        static::assertEquals($sslOptions, $options->getSslOptions());
    }
}
