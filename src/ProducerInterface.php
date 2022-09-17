<?php

namespace RabbitMqModule;

interface ProducerInterface
{
    /**
     * @param array<string, mixed> $properties
     */
    public function publish(string $body, string $routingKey = '', array $properties = []): void;
}
