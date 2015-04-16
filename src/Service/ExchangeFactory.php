<?php

namespace RabbitMqModule\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use RabbitMqModule\Options\Exchange as Options;
use InvalidArgumentException;

class ExchangeFactory extends AbstractFactory
{

    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return 'RabbitMqModule\\Options\\Exchange';
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
        $options = $this->getOptions($serviceLocator, 'exchange');
        return $this->createExchange($serviceLocator, $options);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param  Options $options
     * @throws InvalidArgumentException
     * @return null
     */
    protected function createExchange(ServiceLocatorInterface $serviceLocator, Options $options)
    {

    }
}
