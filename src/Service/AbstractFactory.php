<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use Laminas\Stdlib\AbstractOptions;
use Psr\Container\ContainerInterface;
use RuntimeException;

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

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets options from configuration based on name.
     *
     * @param ContainerInterface $container
     * @param string $key
     * @param null|string $name
     *
     * @throws RuntimeException
     *
     * @return \ArrayObject
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
     * @abstract
     *
     * @return string
     */
    abstract public function getOptionsClass(): string;
}
