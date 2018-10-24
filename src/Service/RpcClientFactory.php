<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Psr\Container\ContainerInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\RpcClient;
use RabbitMqModule\Options\RpcClient as Options;

class RpcClientFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass(): string
    {
        return Options::class;
    }

    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     *
     * @return object
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /* @var $rpcOptions Options */
        $rpcOptions = $this->getOptions($container, 'rpc_client');

        return $this->createClient($container, $rpcOptions);
    }

    /**
     * @param ContainerInterface $container
     * @param Options $options
     *
     * @return RpcClient
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function createClient(ContainerInterface $container, Options $options): RpcClient
    {
        /** @var AbstractConnection $connection */
        $connection = $container->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
        $producer = new RpcClient($connection);
        $producer->setSerializer($options->getSerializer());

        return $producer;
    }
}
