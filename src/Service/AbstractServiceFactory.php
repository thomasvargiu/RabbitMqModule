<?php

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $rName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $rName, array $options = null)
    {
        $mappings = $this->getFactoryMapping($container, $rName);

        if (!$mappings) {
            throw new ServiceNotFoundException();
        }

        $factoryClass = $mappings['factoryClass'];
        /* @var $factory \RabbitMqModule\Service\AbstractFactory */
        $factory = new $factoryClass($mappings['serviceName']);

        return $factory($container, $mappings['serviceName']);
    }


    /**
     *
     * @param ContainerInterface $services
     * @param string                  $rName
     * @return bool
     */
    public function canCreate(ContainerInterface $services, $rName)
    {
        return false !== $this->getFactoryMapping($services, $rName);
    }

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $container
     * @param string                                        $name
     * @param string
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $container, $name, $requestedName)
    {
        // v2 => may need to get parent service locator
        if ($container instanceof AbstractPluginManager) {
            $container = $container->getServiceLocator() ?: $container;
        }

        return $this->canCreate($container, $requestedName);
    }

    /**
     * @param ContainerInterface $container
     * @param                    $name
     *
     * @return bool|array
     */
    private function getFactoryMapping(ContainerInterface $container, $name)
    {
        $matches = [];

        if (!preg_match('/^rabbitmq\.(?P<serviceType>[a-z0-9_]+)\.(?P<serviceName>[a-z0-9_]+)$/', $name, $matches)) {
            return false;
        }

        $config = $container->get('Configuration');
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
     * Create service with name.
     *
     * @param ServiceLocatorInterface $container
     * @param                         $name
     * @param                         $rName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $container, $name, $rName)
    {
        // v2 => may need to get parent service locator
        if ($container instanceof AbstractPluginManager) {
            $container = $container->getServiceLocator() ?: $container;
        }

        return $this($container, $rName);
    }
}
