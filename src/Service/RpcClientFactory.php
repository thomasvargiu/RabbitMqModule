<?php

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\Producer;
use RabbitMqModule\RpcClient;
use Zend\ServiceManager\ServiceLocatorInterface;
use RabbitMqModule\Options\RpcClient as Options;
use InvalidArgumentException;

class RpcClientFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return 'RabbitMqModule\\Options\\RpcClient';
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
        $options = $this->getOptions($container, 'rpc_client');

        return $this->createClient($container, $options);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param Options                 $options
     *
     * @return Producer
     *
     * @throws InvalidArgumentException
     */
    protected function createClient(ServiceLocatorInterface $serviceLocator, Options $options)
    {
        /** @var AbstractConnection $connection */
        $connection = $serviceLocator->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
        $producer = new RpcClient($connection);
        $producer->setSerializer($options->getSerializer());

        return $producer;
    }
}
