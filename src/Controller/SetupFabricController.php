<?php

namespace RabbitMqModule\Controller;

use RabbitMqModule\Service\SetupFabricAwareInterface;
use Zend\Mvc\Controller\AbstractConsoleController;

class SetupFabricController extends AbstractConsoleController
{
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
    protected function getServiceParts()
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

    protected function getServiceKeys($service)
    {
        /** @var array $config */
        $config = $this->getServiceLocator()->get('Configuration');
        if (!isset($config['rabbitmq'][$service])) {
            throw new \RuntimeException(sprintf('No service "rabbitmq.%s" found in configuration', $service));
        }

        return array_keys($config['rabbitmq'][$service]);
    }
}
