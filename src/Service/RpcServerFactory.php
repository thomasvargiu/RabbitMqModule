<?php

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use RabbitMqModule\ConsumerInterface;
use RabbitMqModule\RpcServer;
use Zend\ServiceManager\ServiceLocatorInterface;
use RabbitMqModule\Options\RpcServer as Options;
use InvalidArgumentException;

class RpcServerFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return 'RabbitMqModule\\Options\\RpcServer';
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
        $options = $this->getOptions($container, 'rpc_server');

        return $this->createServer($container, $options);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param Options                 $options
     *
     * @throws InvalidArgumentException
     *
     * @return RpcServer
     */
    protected function createServer(ServiceLocatorInterface $serviceLocator, Options $options)
    {
        $callback = $options->getCallback();
        if (is_string($callback)) {
            $callback = $serviceLocator->get($callback);
        }
        if ($callback instanceof ConsumerInterface) {
            $callback = [$callback, 'execute'];
        }
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Invalid callback provided');
        }

        /** @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $connection = $serviceLocator->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
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
