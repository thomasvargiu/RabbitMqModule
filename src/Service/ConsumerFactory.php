<?php

namespace RabbitMqModule\Service;

use RabbitMqModule\ConsumerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use RabbitMqModule\Options\Consumer as Options;
use InvalidArgumentException;

class ConsumerFactory extends AbstractFactory
{

    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return 'RabbitMqModule\\Options\\Consumer';
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $options Options */
        $options = $this->getOptions($serviceLocator, 'consumer');
        return $this->createConsumer($serviceLocator, $options);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param  Options $options
     * @throws InvalidArgumentException
     * @return null
     */
    protected function createConsumer(ServiceLocatorInterface $serviceLocator, Options $options)
    {
        $callback = $options->getCallback();
    }
}
