<?php

namespace RabbitMqModule\Controller;

use RabbitMqModule\Service\SetupFabricAwareInterface;
use Zend\Console\ColorInterface;

class SetupFabricController extends AbstractConsoleController
{
    public function indexAction()
    {
        /** @var \Zend\Console\Response $response */
        $response = $this->getResponse();
        $this->getConsole()->writeLine('Setting up the AMQP fabric');

        try {
            $services = $this->getServiceParts();

            foreach ($services as $service) {
                if (!$service instanceof SetupFabricAwareInterface) {
                    continue;
                }
                $service->setupFabric();
            }
        } catch (\Exception $e) {
            $response->setErrorLevel(1);
            $this->getConsole()->writeText(sprintf('Exception: %s', $e->getMessage()), ColorInterface::LIGHT_RED);

            return $response;
        }
    }

    /**
     * @return array
     */
    protected function getServiceParts()
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

    protected function getServiceKeys($service)
    {
        /** @var array $config */
        $config = $this->container->get('Configuration');
        if (!isset($config['rabbitmq'][$service])) {
            throw new \RuntimeException(sprintf('No service "rabbitmq.%s" found in configuration', $service));
        }

        return array_keys($config['rabbitmq'][$service]);
    }
}
