<?php

namespace RabbitMqModule\Service;

class AbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOptions()
    {
        $configuration = [
            'rabbitmq' => [
                'default-key' => [
                    'default-name' => [
                        'opt2' => 'value2',
                    ],
                    'name1' => [
                        'opt1' => 'value1',
                    ],
                ],
            ],
        ];
        
        $serviceLocator = static::getMockBuilder('Zend\\ServiceManager\\ServiceLocatorInterface')
            ->setMethods(['get', 'has'])
            ->getMock();
        $factory = static::getMockBuilder('RabbitMqModule\\Service\\AbstractFactory')
            ->setConstructorArgs(['default-name'])
            ->setMethods(['getOptionsClass', 'createService'])
            ->getMock();

        $serviceLocator->method('get')->willReturn($configuration);
        $serviceLocator->method('has')->willReturn(true);
        $factory->method('getOptionsClass')->willReturn('ArrayObject');

        /* @var \RabbitMqModule\Service\AbstractFactory $factory */
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
                        'opt2' => 'value2',
                    ],
                    'name1' => [
                        'opt1' => 'value1',
                    ],
                ],
            ],
        ];

        $serviceLocator = static::getMockBuilder('Zend\\ServiceManager\\ServiceLocatorInterface')
            ->setMethods(['get', 'has'])
            ->getMock();
        $factory = static::getMockBuilder('RabbitMqModule\\Service\\AbstractFactory')
            ->setConstructorArgs(['default-name'])
            ->setMethods(['getOptionsClass', 'createService'])
            ->getMock();

        $serviceLocator->method('get')->willReturn($configuration);

        /* @var \RabbitMqModule\Service\AbstractFactory $factory */
        $factory->getOptions($serviceLocator, 'default-key', 'invalid-key');
    }
}
