<?php

namespace RabbitMqModule\Service\Connection;

use PhpAmqpLib\Connection\AMQPLazyConnection;
use RabbitMqModule\Options\Connection as ConnectionOptions;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class StreamConnectionFactory.
 */
class StreamConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @param ConnectionOptions $options
     *
     * @return AMQPStreamConnection
     */
    public function createConnection(ConnectionOptions $options)
    {
        if ($options->isLazy()) {
            return $this->createLazyConnection($options);
        }

        return $this->createStreamConnection($options);
    }

    /**
     * @param ConnectionOptions $options
     *
     * @return AMQPLazyConnection
     */
    private function createLazyConnection(ConnectionOptions $options)
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

    /**
     * @codeCoverageIgnore
     * @param ConnectionOptions $options
     *
     * @return AMQPStreamConnection
     */
    private function createStreamConnection(ConnectionOptions $options)
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
