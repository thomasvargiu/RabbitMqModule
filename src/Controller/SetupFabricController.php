<?php

declare(strict_types=1);

namespace RabbitMqModule\Controller;

use RabbitMqModule\Service\SetupFabricAwareInterface;
use Laminas\Console\ColorInterface;

/**
 * Class SetupFabricController
 */
class SetupFabricController extends AbstractConsoleController
{
    public function indexAction()
    {
        /** @var \Laminas\Console\Response $response */
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
        } catch (\Throwable $e) {
            $response->setErrorLevel(1);
            $this->getConsole()->writeText(sprintf('Exception: %s', $e->getMessage()), ColorInterface::LIGHT_RED);

            return $response;
        }
    }

    /**
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
     * @param $service
     * @return array
     * @throws \RuntimeException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getServiceKeys($service): array
    {
        /** @var array $config */
        $config = $this->container->get('Configuration');
        if (!isset($config['rabbitmq'][$service])) {
            throw new \RuntimeException(sprintf('No service "rabbitmq.%s" found in configuration', $service));
        }

        return array_keys($config['rabbitmq'][$service]);
    }
}
