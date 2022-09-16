<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Serializer;
use PhpAmqpLib\Connection\AbstractConnection;
use Psr\Container\ContainerInterface;
use RabbitMqModule\Options\RpcClient as Options;
use RabbitMqModule\RpcClient;

/**
 * @extends AbstractFactory<Options, RpcClient>
 */
final class RpcClientFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @psalm-return class-string<Options>
     */
    public function getOptionsClass(): string
    {
        return Options::class;
    }


    public function __invoke(ContainerInterface $container): RpcClient
    {
        $rpcOptions = $this->getOptions($container, 'rpc_client');

        return $this->createClient($container, $rpcOptions);
    }

    protected function createClient(ContainerInterface $container, Options $options): RpcClient
    {
        /** @var AbstractConnection $connection */
        $connection = $container->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
        $producer = new RpcClient($connection);

        $serializer = $options->getSerializer();

        if (is_array($serializer)) {
            $name = $serializer['name'];
            $serializer = Serializer::factory($name, $serializer['options'] ?? null);
        } elseif (is_string($serializer)) {
            /** @var mixed $serializer */
            $serializer = $container->get($serializer);
        }

        if (null !== $serializer && ! $serializer instanceof AdapterInterface) {
            throw new \InvalidArgumentException(sprintf('Invalid serializer instance for rpc_client "%s"', $this->name));
        }

        $producer->setSerializer($serializer);

        return $producer;
    }
}
