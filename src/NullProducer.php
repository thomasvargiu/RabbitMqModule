<?php

declare(strict_types=1);

namespace RabbitMqModule;

/**
 * Class NullProducer. Useful for tests.
 */
class NullProducer implements ProducerInterface
{
    /**
     * @param array<string, mixed> $properties
     */
    public function publish(string $body, string $routingKey = '', array $properties = []): void
    {
        // do nothing
    }
}
