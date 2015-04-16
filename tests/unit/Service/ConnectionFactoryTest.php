<?php

namespace RabbitMqModuleTest\Factory;

use RabbitMqModule\Service\ConnectionFactory;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testGetOptionsClalss()
    {
        $factory = new ConnectionFactory('default');
        $optionsClass = $factory->getOptionsClass();
        static::assertEquals('RabbitMqModule\\Options\\Connection', $optionsClass);
    }

    public function testCreateService()
    {

        $serviceLocator = $this->getMockBuilder('Zend\\ServiceManager\\ServiceLocatorInterface')
            ->getMock();

        $concreteConnectionFactory = $this->getMockBuilder('RabbitMqModule\\Service\\Connection\\ConnectionFactory')
            ->getMock();

        $connectionFactory = $this->getMock('RabbitMqModule\\Service\\ConnectionFactory', ['getOptions'], ['default']);

        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMock();

        $options = $this->getMockBuilder('RabbitMqModule\\Options\\Connection\\AbstractConnection')
            ->getMock();

        $connectionFactory->expects(static::once())
            ->method('getOptions')
            ->will(static::returnValue($options));

        $serviceLocator->expects(static::once())
            ->method('get')
            ->with(static::equalTo('RabbitMqModule\\Service\\Connection\\ConnectionFactory'))
            ->will(static::returnValue($concreteConnectionFactory));

        $concreteConnectionFactory->expects(static::once())
            ->method('createConnection')
            ->with(static::equalTo($options))
            ->will(static::returnValue($connection));

        $service = $connectionFactory->createService($serviceLocator);
        static::assertEquals($service, $connection);
    }

    public function testGetOptions()
    {
        $configuration = [
            'rabbitmq' => [
                'connection' => [
                    'default' => [
                        'type' => 'test-type',
                        'host' => 'www.test.com',
                    ]
                ]
            ]
        ];

        $optionsFactory = $this->getMockBuilder('RabbitMqModule\\Options\\Connection\\ConnectionOptionsFactory')
            ->getMock();

        $options = $this->getMockBuilder('RabbitMqModule\\Options\\Connection\\AbstractConnection')
            ->getMock();

        $options->expects(static::once())
            ->method('setFromArray')
            ->with(static::equalTo($configuration['rabbitmq']['connection']['default']));

        $optionsFactory->expects(static::once())
            ->method('createOptions')
            ->with(static::equalTo('test-type'))
            ->will(static::returnValue($options));

        $serviceLocator = $this->getMockBuilder('Zend\\ServiceManager\\ServiceLocatorInterface')
            ->getMock();

        $serviceLocator->expects(static::any())
            ->method('get')
            ->will(static::returnValueMap([
                ['Configuration', $configuration],
                ['RabbitMqModule\\Options\\Connection\\ConnectionOptionsFactory', $optionsFactory]
            ]));

        $factory = new ConnectionFactory('default');
        $optionsClass = $factory->getOptions($serviceLocator, 'connection');

        static::assertEquals($optionsClass, $options);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Options with name "notexists" could not be found in "rabbitmq.connection"
     */
    public function testGetOptionsWithInvalidName()
    {
        $configuration = [
            'rabbitmq' => [
                'connection' => [
                    'default' => [
                        'type' => 'test-type',
                        'host' => 'www.test.com',
                    ]
                ]
            ]
        ];

        $serviceLocator = $this->getMockBuilder('Zend\\ServiceManager\\ServiceLocatorInterface')
            ->getMock();

        $serviceLocator->expects(static::once())
            ->method('get')
            ->with(static::equalTo('Configuration'))
            ->will(static::returnValue($configuration));

        $factory = new ConnectionFactory('notexists');
        $factory->getOptions($serviceLocator, 'connection');
    }
}
