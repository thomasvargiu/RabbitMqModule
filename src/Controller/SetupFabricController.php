<?php

namespace RabbitMqModule\Controller;

use RabbitMqModule\Service\SetupFabricAwareInterface;
use Zend\Mvc\Controller\AbstractConsoleController;

class SetupFabricController extends AbstractConsoleController
{
    /**
     * @var array
     */
    protected $config;

    public function indexAction()
    {
        $this->getConsole()->writeLine('Setting up the AMQP fabric');

        $services = $this->getServiceParts();

        foreach ($services as $service) {
            if (!$service instanceof SetupFabricAwareInterface) {
                continue;
            }
            $service->setupFabric();
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        if (!$this->config) {
            $this->config = $this->getServiceLocator()->get('Configuration');
        }
        return $this->config;
    }

    /**
     * @return array
     */
    public function getServiceParts()
    {
        $serviceKeys = [
            'consumer',
            'producer'
        ];
        $parts = [];
        foreach ($serviceKeys as $serviceKey) {
            $keys = $this->getServiceKeys($serviceKey);
            foreach ($keys as $key) {
                $parts[] = $this->getServiceLocator()->get(sprintf('rabbitmq.%s.%s', $serviceKey, $key));
            }
        }
        return $parts;
    }

    public function getServiceKeys($service)
    {
        $config = $this->getConfig();
        if (!isset($config['rabbitmq'][$service])) {
            throw new \RuntimeException(sprintf('No service "rabbitmq.%s" found in configuration', $service));
        }
        return array_keys($config['rabbitmq'][$service]);
    }
}
