<?php

namespace RabbitMqModule\Options\Connection;

use RabbitMqModule\Service\Connection\ConnectionFactory;

class ConnectionOptionsFactory
{

    /**
     * @param string $type
     * @return AbstractConnection
     */
    public function createOptions($type)
    {
        switch ($type) {
            case ConnectionFactory::TYPE_SOCKET:
                $optionsClass = new SocketConnection();
                break;

            case ConnectionFactory::TYPE_STREAM:
                $optionsClass = new StreamConnection();
                break;

            case ConnectionFactory::TYPE_SSL:
                $optionsClass = new SSLConnection();
                break;

            default:
                throw new \InvalidArgumentException(sprintf('Connection type "%s" is not available', $type));
        }

        return $optionsClass;
    }
}
