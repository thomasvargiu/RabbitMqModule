<?php

namespace RabbitMqModule;

use PhpAmqpLib\Connection\AbstractConnection;
use PHPUnit\Framework\TestCase;
use RabbitMqModule\Options\Exchange as ExchangeOptions;
use RabbitMqModule\Options\ExchangeBind;
use RabbitMqModule\Options\Queue as QueueOptions;

class ConsumerTest extends TestCase
{
    public function testProperties(): void
    {
        $connection = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        /* @var AbstractConnection $connection */
        $consumer = new Consumer($connection);

        static::assertTrue($consumer->isAutoSetupFabricEnabled());
        static::assertEquals(0, $consumer->getIdleTimeout());

        $queueOptions = new QueueOptions();
        $exchangeOptions = new ExchangeOptions();

        $callback = function (): void {
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

    public function testSetupFabric(): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        $queueOptions = new QueueOptions();
        $queueOptions->setName('foo');
        $exchangeOptions = new ExchangeOptions();

        $exchangeBindOptions = new ExchangeOptions();
        $exchangeBind = new ExchangeBind();
        $exchangeBind->setExchange($exchangeBindOptions);
        $exchangeOptions->setExchangeBinds([$exchangeBind]);

        /* @var AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setQueueOptions($queueOptions);
        $consumer->setExchangeOptions($exchangeOptions);

        $channel->expects(static::exactly(1))
            ->method('exchange_bind');
        $channel->expects(static::exactly(2))
            ->method('exchange_declare');
        $channel->expects(static::once())
            ->method('queue_declare');

        $consumer->setupFabric();
    }

    public function testSetupFabricWithEmptyQueueName(): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        $queueOptions = new QueueOptions();
        $exchangeOptions = new ExchangeOptions();
        $exchangeOptions->setDeclare(false);

        /* @var AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setQueueOptions($queueOptions);
        $consumer->setExchangeOptions($exchangeOptions);

        $channel->expects(static::never())
            ->method('exchange_bind');
        $channel->expects(static::never())
            ->method('exchange_declare');
        $channel->expects(static::never())
            ->method('queue_declare');

        $consumer->setupFabric();
    }

    public function testSetupFabricWithoutQueueOptions(): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        $exchangeOptions = new ExchangeOptions();
        $exchangeOptions->setDeclare(false);

        /* @var AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setExchangeOptions($exchangeOptions);

        $channel->expects(static::never())
            ->method('exchange_bind');
        $channel->expects(static::never())
            ->method('exchange_declare');
        $channel->expects(static::never())
            ->method('queue_declare');

        $consumer->setupFabric();
    }

    public function testSetupFabricWithNoDeclareExchange(): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        $exchangeOptions = new ExchangeOptions();
        $exchangeOptions->setDeclare(false);

        /* @var AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setExchangeOptions($exchangeOptions);

        $channel->expects(static::never())
            ->method('exchange_bind');
        $channel->expects(static::never())
            ->method('exchange_declare');
        $channel->expects(static::never())
            ->method('queue_declare');

        $consumer->setupFabric();
    }

    /**
     * @dataProvider processMessageProvider
     */
    public function testProcessMessage($response, $method, $paramsExpects): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $message = $this->getMockBuilder('PhpAmqpLib\\Message\\AMQPMessage')
            ->disableOriginalConstructor()
            ->getMock();
        $message->delivery_info = [
            'channel' => $channel,
            'delivery_tag' => 'foo',
        ];

        /* @var AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setCallback(function () use ($response) {
            return $response;
        });

        $expect = $channel->expects(static::once())
            ->method($method);
        call_user_func_array([$expect, 'with'], $paramsExpects);

        $consumer->processMessage($message);
    }

    public function testPurge(): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $queueOptions = new QueueOptions();
        $queueOptions->setName('foo');

        $channel->expects(static::once())
            ->method('queue_purge')
            ->with(static::equalTo('foo'), static::equalTo(true));

        /* @var AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setQueueOptions($queueOptions);
        $consumer->purgeQueue();
    }

    public function testStart(): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $exchangeOptions = new ExchangeOptions();
        $exchangeOptions->setName('foo');
        $queueOptions = new QueueOptions();
        $queueOptions->setName('foo');

        $callbacks = [
            static function (): void {
            },
            static function (): void {
            },
            static function (): void {
            },
        ];
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

        /* @var AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setExchangeOptions($exchangeOptions);
        $consumer->setQueueOptions($queueOptions);
        $consumer->start();
    }

    public function testConsume(): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $exchangeOptions = new ExchangeOptions();
        $exchangeOptions->setName('foo');
        $queueOptions = new QueueOptions();
        $queueOptions->setName('foo');

        $callbacks = [
            static function (): void {
            },
            static function (): void {
            },
            static function (): void {
            },
        ];
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

        /* @var AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);
        $consumer->setExchangeOptions($exchangeOptions);
        $consumer->setQueueOptions($queueOptions);
        $consumer->consume();
    }

    public function testConsumeWithStop(): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        /* @var AbstractConnection $connection */
        $consumer = new Consumer($connection, $channel);

        $exchangeOptions = new ExchangeOptions();
        $exchangeOptions->setName('foo');
        $queueOptions = new QueueOptions();
        $queueOptions->setName('foo');

        $callbacks = [
            static function (): void {
            },
            static function (): void {
            },
            static function (): void {
            },
        ];
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
