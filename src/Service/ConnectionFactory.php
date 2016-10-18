<?php

namespace RabbitMqModule\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use InvalidArgumentException;
use RabbitMqModule\Service\Connection\ConnectionFactoryInterface;
use RuntimeException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
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
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return object
     *
     * @throws ServiceNotFoundException   if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *                                    creating a service.
     * @throws ContainerException         if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $options ConnectionOptions */
        $options = $this->getOptions($container, 'connection');
        $factory = $this->getFactory($container, $options->getType());

        return $factory->createConnection($options);
    }

    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'Connection');
    }

    /**
     * @param ContainerInterface $container
     * @param string             $type
     *
     * @return ConnectionFactoryInterface
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function getFactory(ContainerInterface $container, $type)
    {
        $map = $this->getFactoryMap();
        if (!array_key_exists($type, $map)) {
            throw new InvalidArgumentException(sprintf('Options type "%s" not valid', $type));
        }

        $className = $map[$type];
        $factory = $container->get($className);
        if (!$factory instanceof ConnectionFactoryInterface) {
            throw new RuntimeException(
                sprintf('Factory for type "%s" must be an instance of ConnectionFactoryInterface', $type)
            );
        }

        return $factory;
    }
}
