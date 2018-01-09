<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\Producer;
use RabbitMqModule\Options\Producer as Options;

class ProducerFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass(): string
    {
        return \RabbitMqModule\Options\Producer::class;
    }

    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     *
     * @return Producer
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $producerOptions Options */
        $producerOptions = $this->getOptions($container, 'producer');

        return $this->createProducer($container, $producerOptions);
    }

    /**
     * @param ContainerInterface $container
     * @param Options $options
     *
     * @return Producer
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function createProducer(ContainerInterface $container, Options $options): Producer
    {
        /** @var AbstractConnection $connection */
        $connection = $container->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
        $producer = new Producer($connection);
        $producer->setExchangeOptions($options->getExchange());
        if ($options->getQueue()) {
            $producer->setQueueOptions($options->getQueue());
        }
        $producer->setAutoSetupFabricEnabled($options->isAutoSetupFabricEnabled());
        $producer->setReconnectEnabled($options->isReconnectEnabled());

        return $producer;
    }
}
