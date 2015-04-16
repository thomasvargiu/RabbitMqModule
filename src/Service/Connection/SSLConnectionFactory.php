<?php

namespace RabbitMqModule\Service\Connection;

use RabbitMqModule\Options\Connection\SSLConnection as Options;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class SSLConnectionFactory
 *
 * @package RabbitMqModule\Service\Connection
 */
class SSLConnectionFactory
{
    /**
     * @codeCoverageIgnore
     *
     * @param Options $options
     * @return AMQPStreamConnection
     */
    public static function createConnection(Options $options)
    {
        return new AMQPStreamConnection(
            $options->getHost(),
            $options->getPort(),
            $options->getUsername(),
            $options->getPassword(),
            $options->getVhost(),
            $options->isInsist(),
            $options->getLoginMethod(),
            null,
            $options->getLocale(),
            $options->getConnectionTimeout(),
            $options->getReadWriteTimeout(),
            $options->getSslOptions(),
            $options->isKeepAlive(),
            $options->getHeartbeat()
        );
    }
}
