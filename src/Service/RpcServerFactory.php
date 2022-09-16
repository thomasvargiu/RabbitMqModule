<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Serializer;
use function is_string;
use Psr\Container\ContainerInterface;
use RabbitMqModule\Options\RpcServer as Options;
use RabbitMqModule\RpcServer;

/**
 * @extends AbstractFactory<Options, RpcServer>
 * @psalm-import-type ConsumerHandler from \RabbitMqModule\BaseConsumer
 */
final class RpcServerFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     *
     * @psalm-return class-string<Options>
     */
    public function getOptionsClass(): string
    {
        return Options::class;
    }

    public function __invoke(ContainerInterface $container): RpcServer
    {
        $rpcOptions = $this->getOptions($container, 'rpc_server');

        return $this->createServer($container, $rpcOptions);
    }

    protected function createServer(ContainerInterface $container, Options $options): RpcServer
    {
        $callback = ConsumerFactory::getCallback($container, $options);

        $serializer = $options->getSerializer();

        if (is_array($serializer)) {
            $name = $serializer['name'];
            $serializer = Serializer::factory($name, $serializer['options'] ?? null);
        } elseif (is_string($serializer)) {
            /** @var mixed $serializer */
            $serializer = $container->get($serializer);
        }

        if (null !== $serializer && ! $serializer instanceof AdapterInterface) {
            throw new \InvalidArgumentException(sprintf('Invalid serializer instance for rpc_server "%s"', $this->name));
        }

        /** @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $connection = $container->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
        $server = new RpcServer($connection, $options->getQueue(), $callback);
        $server->setExchangeOptions($options->getExchange());
        $server->setConsumerTag($options->getConsumerTag() ?: sprintf('PHPPROCESS_%s_%s', gethostname(), getmypid()));
        $server->setAutoSetupFabricEnabled($options->isAutoSetupFabricEnabled());
        $server->setIdleTimeout($options->getIdleTimeout());
        $server->setSerializer($serializer);

        $qos = $options->getQos();
        if ($qos) {
            $server->setQosOptions(
                $qos->getPrefetchSize(),
                $qos->getPrefetchCount()
            );
        }

        return $server;
    }
}
