<?php

declare(strict_types=1);

namespace RabbitMqModule\Service\Connection;

use PhpAmqpLib\Connection\AbstractConnection;
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
     * @return AMQPLazyConnection
     */
    public function createConnection(ConnectionOptions $options): AbstractConnection
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
