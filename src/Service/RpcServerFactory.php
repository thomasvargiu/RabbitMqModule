<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use RabbitMqModule\ConsumerInterface;
use RabbitMqModule\RpcServer;
use RabbitMqModule\Options\RpcServer as Options;
use InvalidArgumentException;

class RpcServerFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass(): string
    {
        return \RabbitMqModule\Options\RpcServer::class;
    }

    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     *
     * @return object
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $rpcOptions Options */
        $rpcOptions = $this->getOptions($container, 'rpc_server');

        return $this->createServer($container, $rpcOptions);
    }

    /**
     * @param ContainerInterface $container
     * @param Options $options
     *
     * @return RpcServer
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function createServer(ContainerInterface $container, Options $options): RpcServer
    {
        $callback = $options->getCallback();
        if (\is_string($callback)) {
            $callback = $container->get($callback);
        }
        if ($callback instanceof ConsumerInterface) {
            $callback = [$callback, 'execute'];
        }
        if (! \is_callable($callback)) {
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
