<?php

namespace RabbitMqModule\Service;

use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;

class AbstractServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var AbstractServiceFactory
     */
    private $factory;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->factory = new AbstractServiceFactory();
        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setService(
            'Configuration',
            [
                'rabbitmq' => [
                    'producer' => [
                        'custom_Producer-09' => [],
                        'bar' => [],
                        'wrong~name' => [],
                    ],
                    'my_service_type' => [
                        'foo' => []
                    ],
                ],
                'rabbitmq_factories' => [
                    'producer' => 'RabbitMqModule\\Service\\ServiceFactoryMock',
                ],
            ]
        );
    }

    /**
     * @dataProvider getServiceName
     */
    public function testCanCreateServiceWithName($serviceName, $expectedResult)
    {
        $sm = $this->serviceManager;

        static::assertSame($expectedResult, $this->factory->canCreateServiceWithName(
            $sm,
            $serviceName,
            $serviceName
        ));

    }

    public function testItSuccessfullyCreatesConfiguredService()
    {
        static::assertTrue(
            $this->factory->createServiceWithName($this->serviceManager,
                'rabbitmq.producer.bar', 'rabbitmq.producer.bar'
            )
        );
    }

    /**
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testItThrowsServiceNotFoundExceptionForUnknownServiceName()
    {
        $this->factory->createServiceWithName(
            $this->serviceManager,
            'rabbitmq.unknown-key.foo',
            'rabbitmq.unknown-key.foo'
        );
    }

    public function getServiceName()
    {
        return [
            'not configured service type' => [
                'rabbitmq.not_configured_type.foo',
                false
            ],
            'configured but not supported service type' => [
                'rabbitmq.my_service_type.foo',
                false
            ],
            'configured service but with not supported sign' => [
                'rabbitmq.producer.wrong~name',
                false
            ],
            'service type with not supported sign' => [
                'rabbitmq.produ&cer.custom_Producer-09',
                false
            ],
            'correctly configured producer' => [
                'rabbitmq.producer.custom_Producer-09',
                true
            ],
        ];
    }
}
