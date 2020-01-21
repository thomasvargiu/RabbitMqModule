<?php

namespace RabbitMqModule\Service;

use PHPUnit\Framework\TestCase;
use Laminas\ServiceManager\ServiceManager;

class AbstractServiceFactoryTest extends TestCase
{
    /**
     * @var \Interop\Container\ContainerInterface
     */
    protected $serviceManager;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setService(
            'config',
            [
                'rabbitmq' => [
                    'connection' => [
                        'default' => [],
                    ],
                    'producer' => [
                        'foo' => [
                            'exchange' => [],
                        ],
                    ],
                    'foo' => [
                        'bar' => [

                        ],
                        'ab3_-' => [

                        ]
                    ],
                ],
                'rabbitmq_factories' => [
                    'foo' => 'fooFactory',
                    'producer' => 'RabbitMqModule\\Service\\ServiceFactoryMock',
                ],
            ]
        );
    }

    public function testCanCreateServiceWithName()
    {
        $sm = $this->serviceManager;
        $factory = new AbstractServiceFactory();
        static::assertTrue($factory->canCreate($sm, 'rabbitmq.foo.bar'));
        static::assertTrue($factory->canCreate($sm, 'rabbitmq.foo.ab3_-'));
        static::assertFalse($factory->canCreate($sm, 'rabbitmq.foo.bar2'));
    }

    /**
     * @expectedException \Interop\Container\Exception\ContainerException
     */
    public function testCreateServiceUnknown()
    {
        $sm = $this->serviceManager;
        $factory = new AbstractServiceFactory();
        $factory($sm, 'rabbitmq.unknown-key.foo');
    }
}
