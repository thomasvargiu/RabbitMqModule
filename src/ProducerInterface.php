<?php

namespace RabbitMqModule;

use PhpAmqpLib\Channel\AMQPChannel;

interface ProducerInterface
{
    /**
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel;

    /**
     * @param string $body
     * @param string $routingKey
     * @param array  $properties
     */
    public function publish(string $body, string $routingKey = '', array $properties = []): void;
}
