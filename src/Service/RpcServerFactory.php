<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use InvalidArgumentException;
use function is_callable;
use function is_string;
use Psr\Container\ContainerInterface;
use RabbitMqModule\ConsumerInterface;
use RabbitMqModule\Options\RpcServer as Options;
use RabbitMqModule\RpcServer;

/**
 * @extends AbstractFactory<Options>
 */
final class RpcServerFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
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
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): RpcServer
    {
        /* @var $rpcOptions Options */
        $rpcOptions = $this->getOptions($container, 'rpc_server');

        return $this->createServer($container, $rpcOptions);
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function createServer(ContainerInterface $container, Options $options): RpcServer
    {
        $callback = $options->getCallback();
        if (is_string($callback)) {
            $callback = $container->get($callback);
        }
        if ($callback instanceof ConsumerInterface) {
            $callback = [$callback, 'execute'];
        }
        if (! is_callable($callback)) {
            throw new InvalidArgumentException('Invalid callback provided');
        }

        /** @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $connection = $container->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
        $server = new RpcServer($connection);
        $server->setQueueOptions($options->getQueue());
        $server->setExchangeOptions($options->getExchange());
        $server->setConsumerTag($options->getConsumerTag() ?: sprintf('PHPPROCESS_%s_%s', gethostname(), getmypid()));
        $server->setAutoSetupFabricEnabled($options->isAutoSetupFabricEnabled());
        $server->setCallback($callback);
        $server->setIdleTimeout($options->getIdleTimeout());
        $server->setSerializer($options->getSerializer());

        if ($options->getQos()) {
            $server->setQosOptions(
                $options->getQos()->getPrefetchSize(),
                $options->getQos()->getPrefetchCount()
            );
        }

        return $server;
    }
}
