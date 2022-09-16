<?php

declare(strict_types=1);

namespace RabbitMqModule\Service;

use InvalidArgumentException;
use PhpAmqpLib\Connection\AbstractConnection;
use Psr\Container\ContainerInterface;
use RabbitMqModule\Options\Connection as ConnectionOptions;
use RabbitMqModule\Service\Connection\ConnectionFactoryInterface;
use RuntimeException;

/**
 * @extends AbstractFactory<ConnectionOptions, AbstractConnection>
 */
final class ConnectionFactory extends AbstractFactory
{
    /**
     * @var array<string, string>
     *
     *
     * @psalm-var array<string, class-string<Connection\ConnectionFactoryInterface>>
     */
    private $factoryMap = [
        'stream' => Connection\StreamConnectionFactory::class,
        'socket' => Connection\SocketConnectionFactory::class,
        'ssl' => Connection\SSLConnectionFactory::class,
        'lazy' => Connection\LazyConnectionFactory::class,
    ];

    /**
     * @return array<string, string>
     *
     *
     * @psalm-return array<string, class-string<Connection\ConnectionFactoryInterface>>
     */
    public function getFactoryMap(): array
    {
        return $this->factoryMap;
    }

    /**
     * @param array<string, string> $factoryMap
     *
     *
     * @psalm-param array<string, class-string<Connection\ConnectionFactoryInterface>> $factoryMap
     *
     * @return $this
     */
    public function setFactoryMap(array $factoryMap): self
    {
        $this->factoryMap = $factoryMap;

        return $this;
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     *
     * @psalm-return class-string<ConnectionOptions>
     */
    public function getOptionsClass(): string
    {
        return ConnectionOptions::class;
    }

    public function __invoke(ContainerInterface $container): AbstractConnection
    {
        $connectionOptions = $this->getOptions($container, 'connection');

        return $this->getFactory($container, $connectionOptions->getType())->createConnection($connectionOptions);
    }

    protected function getFactory(ContainerInterface $container, string $type): ConnectionFactoryInterface
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
