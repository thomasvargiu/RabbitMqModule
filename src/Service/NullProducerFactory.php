<?php

declare(strict_types = 1);

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use RabbitMqModule\NullProducer;

class NullProducerFactory
{
    /**
     * Create NullProducer.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return NullProducer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new NullProducer();
    }
}
