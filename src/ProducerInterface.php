<?php

namespace RabbitMqModule;

interface ProducerInterface
{
    /**
     * @param string $body
     * @param string $routingKey
     * @param array $properties
     *
     * @return $this
     */
    public function publish($body, $routingKey = '', array $properties = []);
}
