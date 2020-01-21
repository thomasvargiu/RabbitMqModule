<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use PhpAmqpLib\Connection\AbstractConnection;
use Psr\Container\ContainerInterface;
use RabbitMqModule\Options\Producer as Options;
use RabbitMqModule\Producer;

class ProducerFactory extends AbstractFactory
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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return Producer
     */
    public function __invoke(ContainerInterface $container)
    {
        /* @var $producerOptions Options */
        $producerOptions = $this->getOptions($container, 'producer');

        return $this->createProducer($container, $producerOptions);
    }

    /**
     * @param ContainerInterface $container
     * @param Options $options
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return Producer
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

        return $producer;
    }
}
