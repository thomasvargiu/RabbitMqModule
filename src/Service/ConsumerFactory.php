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
 * @extends AbstractFactory<Options, Consumer>
 *
 * @psalm-import-type ConsumerHandler from \RabbitMqModule\BaseConsumer
 */
final class ConsumerFactory extends AbstractFactory
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

    public function __invoke(ContainerInterface $container): Consumer
    {
        /* @var $consumerOptions Options */
        $consumerOptions = $this->getOptions($container, 'consumer');

        return $this->createConsumer($container, $consumerOptions);
    }

    /**
     * @psalm-return ConsumerHandler
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public static function getCallback(ContainerInterface $container, Options $options): callable
    {
        $callback = $options->getCallback();

        if (is_string($callback)) {
            /** @psalm-var ConsumerHandler $callback */
            $callback = $container->get($callback);
        }

        if ($callback instanceof ConsumerInterface) {
            trigger_error(
                'ConsumerInterface is deprecated. Consider using an invokable class',
                E_USER_DEPRECATED
            );
            /** @psalm-var ConsumerHandler $callback */
            $callback = [$callback, 'execute'];
        }
        if (! is_callable($callback)) {
            throw new InvalidArgumentException('Invalid callback provided');
        }

        return $callback;
    }

    protected function createConsumer(ContainerInterface $container, Options $options): Consumer
    {
        $callback = self::getCallback($container, $options);

        /** @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $connection = $container->get(sprintf('rabbitmq.connection.%s', $options->getConnection()));
        $consumer = new Consumer($connection, $options->getQueue(), $callback);
        $consumer->setExchangeOptions($options->getExchange());
        $consumer->setConsumerTag($options->getConsumerTag() ?: sprintf('PHPPROCESS_%s_%s', gethostname(), getmypid()));
        $consumer->setAutoSetupFabricEnabled($options->isAutoSetupFabricEnabled());
        $consumer->setIdleTimeout($options->getIdleTimeout());
        $consumer->setQos($options->getQos());

        return $consumer;
    }
}
