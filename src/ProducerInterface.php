<?php

namespace RabbitMqModule;

interface ProducerInterface
{
    /**
     * @param string $body
     * @param string $routingKey
     * @param array  $properties
     */
    public function publish(string $body, string $routingKey = '', array $properties = []): void;
}
