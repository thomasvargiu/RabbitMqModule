<?php

namespace RabbitMqModule\Controller\Factory;

use RabbitMqModule\Controller\SetupFabricController as Controller;
use Zend\ServiceManager\FactoryInterface;
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
     * @param ServiceLocatorInterface $serviceLocator
     * @return Controller
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager */
        return new Controller($serviceLocator->getServiceLocator());
    }
}
