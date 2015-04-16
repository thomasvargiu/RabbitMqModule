<?php

namespace RabbitMqModule\Service\Connection;

use RabbitMqModule\Options\Connection\StreamConnection as Options;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class StreamConnectionFactory
 *
 * @package RabbitMqModule\Service\Connection
 */
class StreamConnectionFactory
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
            null,
            $options->isKeepAlive(),
            $options->getHeartbeat()
        );
    }
}
