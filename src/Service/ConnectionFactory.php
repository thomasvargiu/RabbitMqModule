<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use RabbitMqModule\Options\Connection as ConnectionOptions;
use RabbitMqModule\Service\Connection\ConnectionFactoryInterface;
use RuntimeException;

class ConnectionFactory extends AbstractFactory
{
    /**
     * @var array
     */
    protected $factoryMap = [
        'stream' => Connection\StreamConnectionFactory::class,
        'socket' => Connection\SocketConnectionFactory::class,
        'ssl' => Connection\SSLConnectionFactory::class,
        'lazy' => Connection\LazyConnectionFactory::class,
    ];

    /**
     * @return array
     */
    public function getFactoryMap()
    {
        return $this->factoryMap;
    }

    /**
     * @param array $factoryMap
     *
     * @return $this
     */
    public function setFactoryMap(array $factoryMap)
    {
        $this->factoryMap = $factoryMap;

        return $this;
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass(): string
    {
        return ConnectionOptions::class;
    }

    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $connectionOptions ConnectionOptions */
        $connectionOptions = $this->getOptions($container, 'connection');
        $factory = $this->getFactory($container, $connectionOptions->getType());

        return $factory->createConnection($connectionOptions);
    }

    /**
     * @param ContainerInterface $container
     * @param string $type
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return ConnectionFactoryInterface
     */
    protected function getFactory(ContainerInterface $container, $type)
    {
        $map = $this->getFactoryMap();
        if (! array_key_exists($type, $map)) {
            throw new InvalidArgumentException(sprintf('Options type "%s" not valid', $type));
        }

        $className = $map[$type];
        $factory = $container->get($className);
        if (! $factory instanceof ConnectionFactoryInterface) {
            throw new RuntimeException(
                sprintf('Factory for type "%s" must be an instance of ConnectionFactoryInterface', $type)
            );
        }

        return $factory;
    }
}
