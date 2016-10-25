<?php

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class AbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $name
     *
     * @return bool|array
     */
    private function getFactoryMapping(ContainerInterface $container, $name)
    {
        $matches = [];

        if (!preg_match('/^rabbitmq\.(?P<serviceType>[a-z0-9_]+)\.(?P<serviceName>[a-z0-9_]+)$/', $name, $matches)) {
            return false;
        }

        $config = $container->get('config');
        $serviceType = $matches['serviceType'];
        $serviceName = $matches['serviceName'];

        if (!isset($config['rabbitmq_factories'][$serviceType], $config['rabbitmq'][$serviceType][$serviceName])) {
            return false;
        }

        return [
            'serviceType' => $serviceType,
            'serviceName' => $serviceName,
            'factoryClass' => $config['rabbitmq_factories'][$serviceType],
        ];
    }

    /**
     * Can the factory create an instance for the service?
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     *
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return false !== $this->getFactoryMapping($container, $requestedName);
    }

    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return object
     *
     * @throws ServiceNotFoundException   if unable to resolve the service
     * @throws ServiceNotCreatedException if an exception is raised when
     *                                    creating a service
     * @throws ContainerException         if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mappings = $this->getFactoryMapping($container, $requestedName);

        if (!$mappings) {
            throw new ServiceNotFoundException();
        }

        $factoryClass = $mappings['factoryClass'];
        /* @var $factory \RabbitMqModule\Service\AbstractFactory */
        $factory = new $factoryClass($mappings['serviceName']);

        return $factory($container, $requestedName, $options);
    }
}
