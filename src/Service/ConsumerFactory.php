<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use InvalidArgumentException;
use function is_callable;
use function is_string;
use Psr\Container\ContainerInterface;
use RabbitMqModule\Consumer;
use RabbitMqModule\ConsumerInterface;
use RabbitMqModule\Options\Consumer as Options;

/**
 * @extends AbstractFactory<Options>
 */
final class ConsumerFactory extends AbstractFactory
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
     * @return Consumer
     */
    public function __invoke(ContainerInterface $container)
    {
        /* @var $consumerOptions Options */
        $consumerOptions = $this->getOptions($container, 'consumer');

        return $this->createConsumer($container, $consumerOptions);
    }

    /**
     * @param ContainerInterface $container
     * @param Options $options
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return Consumer
     */
    protected function createConsumer(ContainerInterface $container, Options $options): Consumer
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
        $consumer = new Consumer($connection);
        $consumer->setQueueOptions($options->getQueue());
        $consumer->setExchangeOptions($options->getExchange());
        $consumer->setConsumerTag($options->getConsumerTag() ?: sprintf('PHPPROCESS_%s_%s', gethostname(), getmypid()));
        $consumer->setAutoSetupFabricEnabled($options->isAutoSetupFabricEnabled());
        $consumer->setCallback($callback);
        $consumer->setIdleTimeout($options->getIdleTimeout());

        if ($options->getQos()) {
            $consumer->setQosOptions(
                $options->getQos()->getPrefetchSize(),
                $options->getQos()->getPrefetchCount()
            );
        }

        return $consumer;
    }
}
