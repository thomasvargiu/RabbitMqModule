<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use PhpAmqpLib\Connection\AbstractConnection;
use Psr\Container\ContainerInterface;
use RabbitMqModule\Options\Producer as Options;
use RabbitMqModule\Producer;

/**
 * @extends AbstractFactory<Options, Producer>
 */
final class ProducerFactory extends AbstractFactory
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

    public function __invoke(ContainerInterface $container): Producer
    {
        /* @var $producerOptions Options */
        $producerOptions = $this->getOptions($container, 'producer');

        return $this->createProducer($container, $producerOptions);
    }

    protected function createProducer(ContainerInterface $container, Options $options): Producer
    {
        /** @var AbstractConnection $connection */
        $connection = $container->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
        $producer = new Producer($connection, $options->getExchange());
        if ($options->getQueue()) {
            $producer->setQueueOptions($options->getQueue());
        }
        $producer->setAutoSetupFabricEnabled($options->isAutoSetupFabricEnabled());

        return $producer;
    }
}
