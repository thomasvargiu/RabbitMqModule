<?php

namespace RabbitMqModule\Controller\Factory;

use RabbitMqModule\Controller\RpcServerController as Controller;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RpcServerControllerFactory.
 */
class RpcServerControllerFactory implements FactoryInterface
{
    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Controller
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator() ?: $serviceLocator;
        }

        return $this($serviceLocator, Controller::class);
    }

    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return Controller
     *
     * @throws ServiceNotFoundException   if unable to resolve the service
     * @throws ServiceNotCreatedException if an exception is raised when
     *                                    creating a service
     * @throws ContainerException         if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Controller($container);
    }
}
