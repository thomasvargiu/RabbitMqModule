<?php

declare(strict_types=1);

namespace RabbitMqModule\Controller;

use Laminas\Console\ColorInterface;
use Laminas\Console\Response;
use RabbitMqModule\Service\SetupFabricAwareInterface;
use RuntimeException;
use Throwable;

/**
 * Class SetupFabricController
 */
class SetupFabricController extends AbstractConsoleController
{
    public function indexAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        $this->getConsole()->writeLine('Setting up the AMQP fabric');

        try {
            $services = $this->getServiceParts();

            foreach ($services as $service) {
                if (! $service instanceof SetupFabricAwareInterface) {
                    continue;
                }
                $service->setupFabric();
            }
        } catch (Throwable $e) {
            $response->setErrorLevel(1);
            $this->getConsole()->writeText(sprintf('Exception: %s', $e->getMessage()), ColorInterface::LIGHT_RED);
        }

        return $response;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return array<int, mixed>
     */
    protected function getServiceParts(): array
    {
        $serviceKeys = [
            'consumer',
            'producer',
            'rpc_client',
            'rpc_server',
        ];
        $parts = [];
        foreach ($serviceKeys as $serviceKey) {
            $keys = $this->getServiceKeys($serviceKey);
            foreach ($keys as $key) {
                $parts[] = $this->container->get(sprintf('rabbitmq.%s.%s', $serviceKey, $key));
            }
        }

        return $parts;
    }

    /**
     * @throws RuntimeException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return array<int, string>
     */
    protected function getServiceKeys(string $service): array
    {
        /** @var array<string, mixed> $config */
        $config = $this->container->get('config');
        if (! isset($config['rabbitmq'][$service])) {
            throw new RuntimeException(sprintf('No service "rabbitmq.%s" found in configuration', $service));
        }

        return array_keys($config['rabbitmq'][$service]);
    }
}
