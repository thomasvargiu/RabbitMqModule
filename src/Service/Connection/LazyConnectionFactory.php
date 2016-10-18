<?php

namespace RabbitMqModule\Service\Connection;

use PhpAmqpLib\Connection\AMQPLazyConnection;
use RabbitMqModule\Options\Connection as ConnectionOptions;

/**
 * Class StreamConnectionFactory.
 *
 * @author Krzysztof Gzocha <kgzocha@gmail.com>
 */
class LazyConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @param ConnectionOptions $options
     *
     * @return AMQPLazyConnection
     */
    public function createConnection(ConnectionOptions $options)
    {
        return new AMQPLazyConnection(
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
