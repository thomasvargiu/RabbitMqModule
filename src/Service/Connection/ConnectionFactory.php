<?php

namespace RabbitMqModule\Service\Connection;

use RabbitMqModule\Options\Connection\ConnectionInterface;

/**
 * Class ConnectionFactory
 *
 * @package RabbitMqModule\Service\Connection
 */
class ConnectionFactory
{
    const TYPE_STREAM = 'stream';
    const TYPE_SOCKET = 'socket';
    const TYPE_SSL = 'ssl';

    /**
     * @var array
     */
    protected static $optionsMap = [
        self::TYPE_STREAM => 'RabbitMqModule\\Service\\Connection\\StreamConnectionFactory',
        self::TYPE_SOCKET => 'RabbitMqModule\\Service\\Connection\\SocketConnectionFactory',
        self::TYPE_SSL => 'RabbitMqModule\\Service\\Connection\\SSLConnectionFactory'
    ];

    /**
     * @codeCoverageIgnore
     *
     * @param ConnectionInterface $options
     * @return mixed
     */
    public function createConnection(ConnectionInterface $options)
    {
        if (!array_key_exists($options->getType(), static::$optionsMap)) {
            throw new \InvalidArgumentException(sprintf('Options type "%s" not valid', $options->getType()));
        }

        $class = static::$optionsMap[$options->getType()];
        $factory = new $class();
        return $factory->createConnection($options);
    }
}
