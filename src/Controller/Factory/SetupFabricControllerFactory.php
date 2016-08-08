<?php

namespace RabbitMqModule\Controller\Factory;

use Interop\Container\ContainerInterface;
use RabbitMqModule\Controller\SetupFabricController as Controller;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SetupFabricControllerFactory
 *
 * @package RabbitMqModule\Controller\Factory
 */
class SetupFabricControllerFactory implements FactoryInterface
{
    /**
     * Create service
     * 
     * @param ContainerInterface|ServiceLocatorInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Controller($container);
    }
}
