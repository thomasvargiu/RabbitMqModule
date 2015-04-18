<?php

namespace RabbitMqModule\Service\Connection;

use RabbitMqModule\Options\Connection as ConnectionOptions;

interface ConnectionFactoryInterface
{
    public function createConnection(ConnectionOptions $options);
}
