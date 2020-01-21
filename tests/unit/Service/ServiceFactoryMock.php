<?php

namespace RabbitMqModule\Service;

use Psr\Container\ContainerInterface;

class ServiceFactoryMock
{
    /**
     * Create service.
     *
     * @param ContainerInterface $container
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $container)
    {
        return true;
    }
}
