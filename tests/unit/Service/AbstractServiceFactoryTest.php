<?php

namespace RabbitMqModule\Service;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

class AbstractServiceFactoryTest extends TestCase
{
    /** @var \Interop\Container\ContainerInterface */
    protected $serviceManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
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
                        ],
                    ],
                ],
                'rabbitmq_factories' => [
                    'foo' => 'fooFactory',
                    'producer' => 'RabbitMqModule\\Service\\ServiceFactoryMock',
                ],
            ]
        );
    }

    public function testCanCreateServiceWithName(): void
    {
        $sm = $this->serviceManager;
        $factory = new AbstractServiceFactory();
        static::assertTrue($factory->canCreate($sm, 'rabbitmq.foo.bar'));
        static::assertTrue($factory->canCreate($sm, 'rabbitmq.foo.ab3_-'));
        static::assertFalse($factory->canCreate($sm, 'rabbitmq.foo.bar2'));
    }

    public function testCreateServiceUnknown(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $sm = $this->serviceManager;
        $factory = new AbstractServiceFactory();
        $factory($sm, 'rabbitmq.unknown-key.foo');
    }
}
