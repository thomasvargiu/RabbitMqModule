<?php

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\RpcClient;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
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
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return object
     *
     * @throws ServiceNotFoundException   if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *                                    creating a service.
     * @throws ContainerException         if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $options Options */
        $options = $this->getOptions($container, 'rpc_client');

        return $this->createClient($container, $options);
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
        return $this($serviceLocator, 'RpcClient');
    }

    /**
     * @param ContainerInterface $container
     * @param Options            $options
     *
     * @return RpcClient
     *
     * @throws InvalidArgumentException
     */
    protected function createClient(ContainerInterface $container, Options $options)
    {
        /** @var AbstractConnection $connection */
        $connection = $container->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
        $producer = new RpcClient($connection);
        $producer->setSerializer($options->getSerializer());

        return $producer;
    }
}
