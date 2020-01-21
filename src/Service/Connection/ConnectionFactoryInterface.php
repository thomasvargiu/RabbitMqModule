<?php

declare(strict_types=1);

namespace RabbitMqModule\Service\Connection;

use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\Options\Connection as ConnectionOptions;

interface ConnectionFactoryInterface
{
    /**
     * @param ConnectionOptions $options
     *
     * @return AbstractConnection
     */
    public function createConnection(ConnectionOptions $options): AbstractConnection;
}
