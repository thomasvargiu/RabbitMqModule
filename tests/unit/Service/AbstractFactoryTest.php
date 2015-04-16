<?php

namespace RabbitMqModuleTest\Service;

use Mockery as m;

class AbstractFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testGetOptions()
    {
        $configuration = [
            'rabbitmq' => [
                'default-key' => [
                    'default-name' => [
                        'opt2' => 'value2'
                    ],
                    'name1' => [
                        'opt1' => 'value1'
                    ]
                ],
            ]
        ];
        $serviceLocator = m::mock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $factory = m::mock('RabbitMqModule\\Service\\AbstractFactory[getOptionsClass]', ['default-name']);

        $serviceLocator->shouldReceive('get')->once()->andReturn($configuration);
        $factory->shouldReceive('getOptionsClass')->once()->andReturn('ArrayObject');

        /** @var \RabbitMqModule\Service\AbstractFactory $factory */
        $ret = $factory->getOptions($serviceLocator, 'default-key');

        static::assertInstanceOf('ArrayObject', $ret);
        static::assertEquals($configuration['rabbitmq']['default-key']['default-name'], $ret->getArrayCopy());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetInvalidOptions()
    {
        $configuration = [
            'rabbitmq' => [
                'default-key' => [
                    'default-name' => [
                        'opt2' => 'value2'
                    ],
                    'name1' => [
                        'opt1' => 'value1'
                    ]
                ],
            ]
        ];
        $serviceLocator = m::mock('Zend\\ServiceManager\\ServiceLocatorInterface');
        $factory = m::mock('RabbitMqModule\\Service\\AbstractFactory[]', ['default-name']);

        $serviceLocator->shouldReceive('get')->once()->andReturn($configuration);

        /** @var \RabbitMqModule\Service\AbstractFactory $factory */
        $ret = $factory->getOptions($serviceLocator, 'default-key', 'invalid-key');
    }
}
