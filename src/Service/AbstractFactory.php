<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Laminas\Stdlib\AbstractOptions;
use Psr\Container\ContainerInterface;
use RuntimeException;

/**
 * @template TOptionsClass as AbstractOptions
 */
abstract class AbstractFactory
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var AbstractOptions
     */
    protected $options;

    public function __construct(string $name)
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
     * @throws RuntimeException
     *
     * @return AbstractOptions
     * @phpstan-return TOptionsClass
     * @psalm-return TOptionsClass
     */
    public function getOptions(ContainerInterface $container, string $key, ?string $name = null)
    {
        if ($name === null) {
            $name = $this->getName();
        }

        $options = $container->get('config');
        $options = $options['rabbitmq'][$key][$name] ?? null;

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
     * @phpstan-return class-string<TOptionsClass>
     * @psalm-return class-string<TOptionsClass>
     */
    abstract public function getOptionsClass(): string;
}
