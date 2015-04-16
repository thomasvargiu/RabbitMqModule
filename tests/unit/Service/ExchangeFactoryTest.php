<?php

namespace RabbitMqModuleTest\Factory;

use RabbitMqModule\Service\ExchangeFactory;

class ExchangeactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testGetOptionsClalss()
    {
        $factory = new ExchangeFactory('default');
        $optionsClass = $factory->getOptionsClass();
        static::assertEquals('RabbitMqModule\\Options\\Exchange', $optionsClass);
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
}
