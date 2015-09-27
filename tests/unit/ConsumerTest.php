<?php

namespace RabbitMqModuleTest;

use RabbitMqModule\ConsumerInterface;
use RabbitMqModule\Options\ExchangeBind;
use RabbitMqModule\Options\Queue as QueueOptions;
use RabbitMqModule\Options\Exchange as ExchangeOptions;
use RabbitMqModule\Consumer;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    public function testProperties()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $consumer = new Consumer($connection);

        static::assertTrue($consumer->isAutoSetupFabricEnabled());
        static::assertEquals(0, $consumer->getIdleTimeout());

        $queueOptions = new QueueOptions();
        $exchangeOptions = new ExchangeOptions();

        $callback = function () {

        };

        $consumer->setConsumerTag('consumer-tag-test');
        $consumer->setCallback($callback);
        $consumer->setQueueOptions($queueOptions);
        $consumer->setExchangeOptions($exchangeOptions);
        $consumer->setAutoSetupFabricEnabled(false);
        $consumer->setIdleTimeout(5);

        static::assertSame($connection, $consumer->getConnection());
        static::assertSame($callback, $consumer->getCallback());
        static::assertSame($queueOptions, $consumer->getQueueOptions());
        static::assertSame($exchangeOptions, $consumer->getExchangeOptions());
        static::assertFalse($consumer->isAutoSetupFabricEnabled());
        static::assertEquals(5, $consumer->getIdleTimeout());
    }

    public function testSetupFabric()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        $queueOptions = new QueueOptions();
        $queueOptions->setName('foo');
        $exchangeOptions = new ExchangeOptions();

        $exchangeBindOptions = new ExchangeOptions();
        $exchangeBind = new ExchangeBind();
        $exchangeBind->setExchange($exchangeBindOptions);
        $exchangeOptions->setExchangeBinds([$exchangeBind]);

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setQueueOptions($queueOptions);
        $consumer->setExchangeOptions($exchangeOptions);

        $channel->expects(static::exactly(1))
            ->method('exchange_bind');
        $channel->expects(static::exactly(2))
            ->method('exchange_declare');
        $channel->expects(static::once())
            ->method('queue_declare');

        static::assertSame($consumer, $consumer->setupFabric());
    }

    /**
     * @dataProvider processMessageProvider
     */
    public function testProcessMessage($response, $method, $paramsExpects)
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $message = static::getMockBuilder('PhpAmqpLib\\Message\\AMQPMessage')
            ->disableOriginalConstructor()
            ->getMock();
        $message->delivery_info = [
            'channel' => $channel,
            'delivery_tag' => 'foo',
        ];

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setCallback(function () use ($response) {
            return $response;
        });

        $expect = $channel->expects(static::once())
            ->method($method);
        call_user_func_array([$expect, 'with'], $paramsExpects);

        $consumer->processMessage($message);
    }

    public function testPurge()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $queueOptions = new QueueOptions();
        $queueOptions->setName('foo');

        $channel->expects(static::once())
            ->method('queue_purge')
            ->with(static::equalTo('foo'), static::equalTo(true));

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setQueueOptions($queueOptions);
        $consumer->purgeQueue();
    }

    public function testStart()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $exchangeOptions = new ExchangeOptions();
        $exchangeOptions->setName('foo');
        $queueOptions = new QueueOptions();
        $queueOptions->setName('foo');

        $callbacks = range(0, 2);
        $channel->callbacks = $callbacks;
        $channel->expects(static::exactly(count($callbacks)))
            ->method('wait')
            ->willReturnCallback(function () use ($channel) {
                array_shift($channel->callbacks);

                return true;
            });

        $channel->expects(static::once())
            ->method('basic_consume');
        $channel->expects(static::exactly(count($callbacks)))
            ->method('wait');

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setExchangeOptions($exchangeOptions);
        $consumer->setQueueOptions($queueOptions);
        $consumer->start();
    }

    public function testConsume()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $exchangeOptions = new ExchangeOptions();
        $exchangeOptions->setName('foo');
        $queueOptions = new QueueOptions();
        $queueOptions->setName('foo');

        $callbacks = range(0, 2);
        $channel->callbacks = $callbacks;
        $channel->expects(static::exactly(count($callbacks)))
            ->method('wait')
            ->willReturnCallback(function () use ($channel) {
                array_shift($channel->callbacks);

                return true;
            });

        $channel->expects(static::once())
            ->method('basic_consume');
        $channel->expects(static::exactly(count($callbacks)))
            ->method('wait');

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setExchangeOptions($exchangeOptions);
        $consumer->setQueueOptions($queueOptions);
        $consumer->consume();
    }

    public function testConsumeWithStop()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);

        $exchangeOptions = new ExchangeOptions();
        $exchangeOptions->setName('foo');
        $queueOptions = new QueueOptions();
        $queueOptions->setName('foo');

        $callbacks = range(0, 2);
        $channel->callbacks = $callbacks;
        $channel->expects(static::atLeast(1))
            ->method('wait')
            ->willReturnCallback(function () use ($channel, $consumer) {
                array_shift($channel->callbacks);
                $consumer->forceStopConsumer();

                return true;
            });

        $channel->expects(static::once())
            ->method('basic_consume');
        $channel->expects(static::once())
            ->method('basic_cancel')
            ->willReturnCallback(function () use ($channel) {
                $channel->callbacks = [];

                return true;
            });
        $channel->expects(static::atLeast(1))
            ->method('wait');

        $consumer->setExchangeOptions($exchangeOptions);
        $consumer->setQueueOptions($queueOptions);
        $consumer->consume();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCallbackWithInvalidValue()
    {
        $connection = static::getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = static::getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);

        $consumer->setCallback('string');
    }

    public function processMessageProvider()
    {
        return [
            [
                ConsumerInterface::MSG_ACK,
                'basic_ack',
                [
                    static::equalTo('foo'),
                ],
            ],
            [
                ConsumerInterface::MSG_REJECT,
                'basic_reject',
                [
                    static::equalTo('foo'),
                    static::equalTo(false),
                ],
            ],
            [
                ConsumerInterface::MSG_REJECT_REQUEUE,
                'basic_reject',
                [
                    static::equalTo('foo'),
                    static::equalTo(true),
                ],
            ],
            [
                ConsumerInterface::MSG_SINGLE_NACK_REQUEUE,
                'basic_nack',
                [
                    static::equalTo('foo'),
                    static::equalTo(false),
                    static::equalTo(true),
                ],
            ],
        ];
    }
}
