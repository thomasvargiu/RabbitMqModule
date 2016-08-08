<?php

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use RabbitMqModule\Service\Connection\ConnectionFactoryInterface;
use RuntimeException;
use Zend\ServiceManager\ServiceLocatorInterface;
use RabbitMqModule\Options\Connection as ConnectionOptions;

class ConnectionFactory extends AbstractFactory
{
    /**
     * @var array
     */
    protected $factoryMap = [
        'stream' => 'RabbitMqModule\\Service\\Connection\\StreamConnectionFactory',
        'socket' => 'RabbitMqModule\\Service\\Connection\\SocketConnectionFactory',
        'ssl' => 'RabbitMqModule\\Service\\Connection\\SSLConnectionFactory',
        'lazy' => 'RabbitMqModule\\Service\\Connection\\LazyConnectionFactory',
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
    public function getOptionsClass()
    {
        return 'RabbitMqModule\\Options\\Connection';
    }

    /**
     * Create service.
     *
     * @param ContainerInterface | ServiceLocatorInterface $container
     * @param string $rName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $rName, array $options = null)
    {
        /* @var $options ConnectionOptions */
        $options = $this->getOptions($container, 'connection');
        $factory = $this->getFactory($container, $options->getType());

        return $factory->createConnection($options);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $type
     *
     * @return ConnectionFactoryInterface
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function getFactory(ServiceLocatorInterface $serviceLocator, $type)
    {
        $map = $this->getFactoryMap();
        if (!array_key_exists($type, $map)) {
            throw new InvalidArgumentException(sprintf('Options type "%s" not valid', $type));
        }

        $className = $map[$type];
        $factory = $serviceLocator->get($className);
        if (!$factory instanceof ConnectionFactoryInterface) {
            throw new RuntimeException(
                sprintf('Factory for type "%s" must be an instance of ConnectionFactoryInterface', $type)
            );
        }

        return $factory;
    }
}
