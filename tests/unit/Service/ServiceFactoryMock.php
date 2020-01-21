<?php

namespace RabbitMqModule\Service;

use Psr\Container\ContainerInterface;

class ServiceFactoryMock
{
    /**
     * Create service.
     */
    public function __invoke(ContainerInterface $container)
    {
        return true;
    }
}
