<?php

namespace RabbitMqModuleTest;

use RabbitMqModule\Options\Queue as QueueOptions;
use RabbitMqModule\Options\Exchange as ExchangeOptions;
use RabbitMqModule\Producer;

class ProducerTest extends \PHPUnit_Framework_TestCase
{

    public function testProperties()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $rabbitMqService = static::getMockBuilder('RabbitMqModule\\Service\\RabbitMqService')
            ->getMock();

        $producer = new Producer($connection);

        static::assertSame($connection, $producer->getConnection());
        static::assertNull($producer->getRabbitMqService());
        static::assertEquals('text/plain', $producer->getContentType());
        static::assertEquals(2, $producer->getDeliveryMode());
        static::assertInstanceOf('RabbitMqModule\\Options\\Producer', $producer->getOptions());

        $options = new \RabbitMqModule\Options\Producer;
        $queueOptions = new QueueOptions();
        $exchangeOptions = new ExchangeOptions();
        $options->setQueue($queueOptions);
        $options->setExchange($exchangeOptions);

        $producer->setDeliveryMode(-1);
        $producer->setContentType('foo');
        $producer->setRabbitMqService($rabbitMqService);
        $producer->setOptions($options);

        static::assertSame($connection, $producer->getConnection());
        static::assertSame($rabbitMqService, $producer->getRabbitMqService());
        static::assertEquals('foo', $producer->getContentType());
        static::assertEquals(-1, $producer->getDeliveryMode());
        static::assertSame($options, $producer->getOptions());
        static::assertSame($queueOptions, $producer->getQueueOptions());
        static::assertSame($exchangeOptions, $producer->getExchangeOptions());
    }

    public function testSetupFabric()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $rabbitMqService = static::getMockBuilder('RabbitMqModule\\Service\\RabbitMqService')
            ->setMethods(['declareExchange', 'declareQueue'])
            ->getMock();

        $options = new \RabbitMqModule\Options\Producer;
        $queueOptions = new QueueOptions();
        $exchangeOptions = new ExchangeOptions();
        $options->setQueue($queueOptions);
        $options->setExchange($exchangeOptions);

        $producer = new Producer($connection, $channel);
        $producer->setRabbitMqService($rabbitMqService);
        $producer->setOptions($options);

        $rabbitMqService->expects(static::once())
            ->method('declareExchange')
            ->with($channel, $exchangeOptions);

        $rabbitMqService->expects(static::once())
            ->method('declareQueue')
            ->with($channel, $exchangeOptions, $queueOptions);


        static::assertSame($producer, $producer->setupFabric());
    }
}
