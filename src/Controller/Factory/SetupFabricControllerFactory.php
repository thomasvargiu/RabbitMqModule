<?php

declare(strict_types=1);

namespace RabbitMqModule\Controller\Factory;

use RabbitMqModule\Controller\SetupFabricController as Controller;
use Psr\Container\ContainerInterface;

/**
 * Class SetupFabricControllerFactory.
 */
class SetupFabricControllerFactory
{
    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     *
     * @return Controller
     */
    public function __invoke(ContainerInterface $container): Controller
    {
        return new Controller($container);
    }
}
