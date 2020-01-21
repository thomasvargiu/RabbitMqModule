<?php

declare(strict_types=1);

namespace RabbitMqModule\Controller\Factory;

use Psr\Container\ContainerInterface;
use RabbitMqModule\Controller\ConsumerController as Controller;

/**
 * Class ConsumerControllerFactory.
 */
class ConsumerControllerFactory
{
    /**
     * Create an object.
     */
    public function __invoke(ContainerInterface $container): Controller
    {
        return new Controller($container);
    }
}
