<?php

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
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
     * @param ContainerInterface | ServiceLocatorInterface $container
     * @param string $rName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $rName, array $options = null)
    {
        /* @var $options Options */
        $options = $this->getOptions($container, 'producer');

        return $this->createProducer($container, $options);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param Options                 $options
     *
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
