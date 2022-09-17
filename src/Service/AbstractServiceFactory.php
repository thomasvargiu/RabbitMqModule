<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Container\ContainerInterface;
use RabbitMqModule\ConfigProvider;

/**
 * @psalm-import-type ConfigArray from ConfigProvider
 */
class AbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @psalm-return false|array{serviceType: string, serviceName: string, factoryClass: class-string<AbstractFactory>}
     *
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

        /** @psalm-var ConfigArray $config */
        $config = $container->get('config');
        $serviceType = $matches['serviceType'];
        $serviceName = $matches['serviceName'];

        if (! isset($config['rabbitmq'][$serviceType][$serviceName])) {
            return false;
        }

        $factoryClass = $config['rabbitmq_factories'][$serviceType] ?? null;
        if (! is_string($factoryClass)) {
            return false;
        }

        return [
            'serviceType' => $serviceType,
            'serviceName' => $serviceName,
            'factoryClass' => $factoryClass,
        ];
    }

    /**
     * Can the factory create an instance for the service?
     *
     * @param string $requestedName
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
     * @param null|array<mixed> $options
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object
    {
        $mappings = $this->getFactoryMapping($container, $requestedName);

        if (! $mappings) {
            throw new ServiceNotFoundException();
        }

        $factoryClass = $mappings['factoryClass'];
        $factory = new $factoryClass($mappings['serviceName']);

        return $factory($container);
    }
}
