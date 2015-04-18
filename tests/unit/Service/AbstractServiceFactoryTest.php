<?php
namespace RabbitMqModule\Service;

use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;

class AbstractServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setService(
            'Configuration',
            [
                'rabbitmq' => [
                    'foo' => [
                        'bar' => [

                        ]
                    ]
                ],
                'rabbitmq_factories' => [
                    'foo' => 'fooFactory'
                ]
            ]
        );
    }

    public function testCanCreateServiceWithName()
    {
        $sm = $this->serviceManager;
        $factory = new AbstractServiceFactory();
        static::assertTrue($factory->canCreateServiceWithName($sm, 'rabbitmq.foo.bar', 'rabbitmq.foo.bar'));
        static::assertFalse($factory->canCreateServiceWithName($sm, 'rabbitmq.foo.bar', 'rabbitmq.foo.bar2'));
    }
}
