<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * Interface ConsumerInterface
 *
 * @package RabbitMqModule
 */
interface ConsumerInterface
{
    /**
     * @param AMQPMessage $message
     * @return mixed
     */
    public function execute(AMQPMessage $message);
}
