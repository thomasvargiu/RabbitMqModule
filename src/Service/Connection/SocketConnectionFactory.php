<?php

namespace RabbitMqModule\Service\Connection;

use RabbitMqModule\Options\Connection\SocketConnection as Options;
use PhpAmqpLib\Connection\AMQPSocketConnection;

/**
 * Class SocketConnectionFactory
 *
 * @package RabbitMqModule\Service\Connection
 */
class SocketConnectionFactory
{
    /**
     * @codeCoverageIgnore
     *
     * @param Options $options
     * @return AMQPSocketConnection
     */
    public static function createConnection(Options $options)
    {
        return new AMQPSocketConnection(
            $options->getHost(),
            $options->getPort(),
            $options->getUsername(),
            $options->getPassword(),
            $options->getVhost(),
            $options->isInsist(),
            $options->getLoginMethod(),
            null,
            $options->getLocale(),
            $options->getReadWriteTimeout(),
            $options->isKeepAlive()
        );
    }
}
