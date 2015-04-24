<?php

namespace RabbitMqModule\Service;

use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\Producer;
use Zend\ServiceManager\ServiceLocatorInterface;
use RabbitMqModule\Options\Producer as Options;
use InvalidArgumentException;

class ProducerFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return 'RabbitMqModule\\Options\\Producer';
    }

    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $options Options */
        $options = $this->getOptions($serviceLocator, 'producer');

        return $this->createProducer($serviceLocator, $options);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param Options                 $options
     * @return Producer
     *
     * @throws InvalidArgumentException
     */
    protected function createProducer(ServiceLocatorInterface $serviceLocator, Options $options)
    {
        /** @var AbstractConnection $connection */
        $connection = $serviceLocator->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
        $producer = new Producer($connection);
        $producer->setExchangeOptions($options->getExchange());
        if ($options->getQueue()) {
            $producer->setQueueOptions($options->getQueue());
        }
        $producer->setAutoSetupFabricEnabled($options->isAutoSetupFabricEnabled());

        return $producer;
    }
}
