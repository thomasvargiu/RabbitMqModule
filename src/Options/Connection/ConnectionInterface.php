<?php

namespace RabbitMqModule\Options\Connection;

/**
 * @codeCoverageIgnore
 */
interface ConnectionInterface
{
    /**
     * @return string
     */
    public function getType();
}
