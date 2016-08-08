<?php

namespace RabbitMqModule\Controller\Factory;

use Interop\Container\ContainerInterface;
use RabbitMqModule\Controller\ConsumerController as Controller;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ConsumerControllerFactory
 *
 * @package RabbitMqModule\Controller\Factory
 */
class ConsumerControllerFactory implements FactoryInterface
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
