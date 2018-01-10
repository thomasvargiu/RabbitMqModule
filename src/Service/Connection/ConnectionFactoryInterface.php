<?php

declare(strict_types=1);

namespace RabbitMqModule\Service\Connection;

use RabbitMqModule\Options\Connection as ConnectionOptions;
use PhpAmqpLib\Connection\AbstractConnection;

interface ConnectionFactoryInterface
{
    /**
     * @param ConnectionOptions $options
     * @return AbstractConnection
     */
    public function createConnection(ConnectionOptions $options): AbstractConnection;
}
