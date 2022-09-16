<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Psr\Container\ContainerInterface;
use RabbitMqModule\ConfigProvider;
use RuntimeException;
use RabbitMqModule\Options\AbstractOptions;

/**
 * @template TOptionsClass as AbstractOptions
 * @template-covariant R as object
 * @psalm-import-type ConfigArray from ConfigProvider
 */
abstract class AbstractFactory
{
    protected string $name;

    final public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets options from configuration based on name.
     *
     *
     * @psalm-return TOptionsClass
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getOptions(ContainerInterface $container, string $key, ?string $name = null)
    {
        if ($name === null) {
            $name = $this->getName();
        }

        /** @psalm-var ConfigArray $config */
        $config = $container->get('config');
        $options = $config['rabbitmq'][$key][$name] ?? null;

        if (null === $options) {
            throw new RuntimeException(
                sprintf('Options with name "%s" could not be found in "rabbitmq.%s"', $name, $key)
            );
        }

        $optionsClass = $this->getOptionsClass();

        return new $optionsClass($options);
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     * @psalm-return class-string<TOptionsClass>
     */
    abstract public function getOptionsClass(): string;

    /**
     * @psalm-return R
     */
    abstract public function __invoke(ContainerInterface $container): object;
}
