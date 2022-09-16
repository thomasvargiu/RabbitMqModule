<?php

declare(strict_types=1);

namespace RabbitMqModule\Service\Connection;

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPSocketConnection;
use RabbitMqModule\Options\Connection as ConnectionOptions;

/**
 * Class SocketConnectionFactory.
 */
class SocketConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @codeCoverageIgnore
     *
     * @param ConnectionOptions $options
     * @return AMQPSocketConnection
     * @throws \Exception
     */
    public function createConnection(ConnectionOptions $options): AbstractConnection
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
