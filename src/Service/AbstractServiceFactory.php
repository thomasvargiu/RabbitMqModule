<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

class AbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return bool|array<string, string>
     */
    private function getFactoryMapping(ContainerInterface $container, string $name)
    {
        $matches = [];

        if (! preg_match('/^rabbitmq\.(?P<serviceType>[a-z0-9_]+)\.(?P<serviceName>[a-z0-9_-]+)$/', $name, $matches)) {
            return false;
        }

        $config = $container->get('config');
        $serviceType = $matches['serviceType'];
        $serviceName = $matches['serviceName'];

        if (! isset($config['rabbitmq_factories'][$serviceType], $config['rabbitmq'][$serviceType][$serviceName])) {
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
     * @param string $requestedName
     *
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
     * @param string $requestedName
     * @param null|array<mixed> $options
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mappings = $this->getFactoryMapping($container, $requestedName);

        if (! $mappings) {
            throw new ServiceNotFoundException();
        }

        $factoryClass = $mappings['factoryClass'];
        /* @var callable $factory */
        $factory = new $factoryClass($mappings['serviceName']);

        return $factory($container, $requestedName, $options);
    }
}
