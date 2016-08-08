<?php

namespace RabbitMqModule\Controller\Factory;

use Interop\Container\ContainerInterface;
use RabbitMqModule\Controller\StdInProducerController as Controller;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class StdInProducerController
 *
 * @package RabbitMqModule\Controller\Factory
 */
class StdInProducerControllerFactory implements FactoryInterface
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
