<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;

class AbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $name
     *
     * @return bool|array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function getFactoryMapping(ContainerInterface $container, string $name)
    {
        $matches = [];

        if (!preg_match('/^rabbitmq\.(?P<serviceType>[a-z0-9_]+)\.(?P<serviceName>[a-z0-9_-]+)$/', $name, $matches)) {
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
     * @param string $requestedName
     *
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return false !== $this->getFactoryMapping($container, $requestedName);
    }

    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     *
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mappings = $this->getFactoryMapping($container, $requestedName);

        if (!$mappings) {
            throw new ServiceNotFoundException();
        }

        $factoryClass = $mappings['factoryClass'];
        /* @var callable $factory */
        $factory = new $factoryClass($mappings['serviceName']);

        return $factory($container, $requestedName, $options);
    }
}
