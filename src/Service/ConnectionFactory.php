<?php

namespace RabbitMqModule\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use RabbitMqModule\Options\Connection\AbstractConnection as ConnectionOptions;
use RabbitMqModule\Service\Connection\ConnectionFactory as ConcreteFactory;
use RuntimeException;

class ConnectionFactory extends AbstractFactory
{

    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return 'RabbitMqModule\\Options\\Connection';
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $options ConnectionOptions */
        $options = $this->getOptions($serviceLocator, 'connection');
        /** @var \RabbitMqModule\Service\Connection\ConnectionFactory $factory */
        $factory = $serviceLocator->get('RabbitMqModule\\Service\\Connection\\ConnectionFactory');
        return $factory->createConnection($options);
    }

    /**
     * Gets options from configuration based on name.
     *
     * @param  ServiceLocatorInterface      $sl
     * @param  string                       $key
     * @param  null|string                  $name
     * @return \Zend\Stdlib\AbstractOptions
     * @throws \RuntimeException
     */
    public function getOptions(ServiceLocatorInterface $sl, $key, $name = null)
    {
        if ($name === null) {
            $name = $this->getName();
        }

        $options = $sl->get('Configuration');
        $options = $options['rabbitmq'];
        $options = isset($options[$key][$name]) ? $options[$key][$name] : null;

        if (null === $options) {
            throw new RuntimeException(
                sprintf(
                    'Options with name "%s" could not be found in "rabbitmq.%s"',
                    $name,
                    $key
                )
            );
        }

        $type = isset($options['type']) ? $options['type'] : ConcreteFactory::TYPE_STREAM;

        /** @var \RabbitMqModule\Options\Connection\ConnectionOptionsFactory $optionsFactory */
        $optionsFactory = $sl->get('RabbitMqModule\\Options\\Connection\\ConnectionOptionsFactory');
        $optionsClass = $optionsFactory->createOptions($type);
        $optionsClass->setFromArray($options);

        return $optionsClass;
    }
}
