<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use PhpAmqpLib\Connection\AbstractConnection;
use Psr\Container\ContainerInterface;
use RabbitMqModule\Options\RpcClient as Options;
use RabbitMqModule\RpcClient;

/**
 * @extends AbstractFactory<Options>
 */
final class RpcClientFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     * @phpstan-return class-string<Options>
     * @psalm-return class-string<Options>
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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return RpcClient
     */
    public function __invoke(ContainerInterface $container): RpcClient
    {
        /* @var $rpcOptions Options */
        $rpcOptions = $this->getOptions($container, 'rpc_client');

        return $this->createClient($container, $rpcOptions);
    }

    /**
     * @param ContainerInterface $container
     * @param Options $options
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return RpcClient
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
