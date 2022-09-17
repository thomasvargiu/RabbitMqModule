<?php

namespace RabbitMqModule;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;
use RabbitMqModule\Options\Exchange as ExchangeOptions;
use RabbitMqModule\Options\Queue as QueueOptions;

class ConsumerTest extends TestCase
{
    public function testProperties(): void
    {
        $connection = $this->prophesize(AbstractConnection::class);

        $queueOptions = new QueueOptions();
        $callback = function (): void {
        };
        $consumer = new Consumer($connection->reveal(), $queueOptions, $callback);

        static::assertTrue($consumer->isAutoSetupFabricEnabled());
        static::assertEquals(0, $consumer->getIdleTimeout());

        $exchangeOptions = new ExchangeOptions();

        $consumer->setConsumerTag('consumer-tag-test');
        $consumer->setCallback($callback);
        $consumer->setQueueOptions($queueOptions);
        $consumer->setExchangeOptions($exchangeOptions);
        $consumer->setAutoSetupFabricEnabled(false);
        $consumer->setIdleTimeout(5);

        static::assertSame($connection->reveal(), $consumer->getConnection());
        static::assertSame($callback, $consumer->getCallback());
        static::assertSame($queueOptions, $consumer->getQueueOptions());
        static::assertSame($exchangeOptions, $consumer->getExchangeOptions());
        static::assertFalse($consumer->isAutoSetupFabricEnabled());
        static::assertEquals(5, $consumer->getIdleTimeout());
    }

    public function testSetupFabric(): void
    {
        $channel = $this->prophesize(AMQPChannel::class);
        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $queueOptions = QueueOptions::fromArray([
            'name' => 'foo',
            'routing_keys' => ['foo', 'bar'],
        ]);

        $exchangeOptions = ExchangeOptions::fromArray([
            'name' => 'exchange-name',
            'exchange_binds' => [
                ['exchange' => ['name' => 'exchange-bind'], 'routing_keys' => ['baz', 'faz']],
            ],
        ]);

        $callback = function (): void {
        };

        $consumer = new Consumer($connection->reveal(), $queueOptions, $callback);
        $consumer->setExchangeOptions($exchangeOptions);

        $channel->queue_bind('queue-name', 'exchange-name', 'foo')->shouldBeCalledOnce();
        $channel->queue_bind('queue-name', 'exchange-name', 'bar')->shouldBeCalledOnce();
        $channel->exchange_bind('exchange-name', 'exchange-bind', 'baz')->shouldBeCalledOnce();
        $channel->exchange_bind('exchange-name', 'exchange-bind', 'faz')->shouldBeCalledOnce();
        $channel->exchange_declare(
            'exchange-name',
            'direct',
            false,
            true,
            false,
            false,
            false,
            [],
            0
        )->shouldBeCalled();
        $channel->exchange_declare(
            'exchange-bind',
            'direct',
            false,
            true,
            false,
            false,
            false,
            [],
            0
        )->shouldBeCalled();
        $channel->queue_declare(
            'foo',
            false,
            true,
            false,
            false,
            false,
            [],
            0
        )->shouldBeCalledOnce()->willReturn(['queue-name']);

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
        $consumer = new Consumer($connection, $queueOptions, fn () => null);
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

    public function testSetupFabricWithNoDeclareExchange(): void
    {
        $channel = $this->prophesize(AMQPChannel::class);
        $connection = $this->prophesize(AbstractConnection::class);

        $connection->channel()->willReturn($channel->reveal());

        $queueOptions = QueueOptions::fromArray(['name' => 'foo']);

        $exchangeOptions = ExchangeOptions::fromArray(['name' => 'bar']);
        $exchangeOptions->setDeclare(false);

        $consumer = new Consumer($connection->reveal(), $queueOptions, fn () => null);
        $consumer->setExchangeOptions($exchangeOptions);

        $channel->exchange_bind(Argument::cetera())->shouldNotBeCalled();
        $channel->exchange_declare(Argument::cetera())->shouldNotBeCalled();
        $channel->queue_declare(Argument::cetera())->shouldBeCalled()->willReturn(['foo']);
        $channel->queue_bind('foo', 'bar', '')->shouldBeCalled();

        $consumer->setupFabric();
    }

    /**
     * @dataProvider processMessageProvider
     */
    public function testProcessMessage($response, $method, $paramsExpects): void
    {
        $channel = $this->prophesize(AMQPChannel::class);
        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $message = new AMQPMessage('foo');
        $message->setChannel($channel->reveal());
        $message->setDeliveryTag('foo');

        $queueOptions = QueueOptions::fromArray(['name' => 'foo']);

        $consumer = new Consumer($connection->reveal(), $queueOptions, fn () => $response);

        $channel->$method(...$paramsExpects)->shouldBeCalledOnce();

        $consumer->processMessage($message);
    }

    public function testPurge(): void
    {
        $channel = $this->prophesize(AMQPChannel::class);
        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $queueOptions = QueueOptions::fromArray(['name' => 'foo']);

        $channel->queue_purge('foo', true)->shouldBeCalledOnce();

        $consumer = new Consumer($connection->reveal(), $queueOptions, fn () => null);
        $consumer->setQueueOptions($queueOptions);
        $consumer->purgeQueue();
    }

    public function testStart(): void
    {
        $channel = $this->prophesize(AMQPChannel::class);
        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $queueOptions = QueueOptions::fromArray(['name' => 'foo']);

        $channel->is_consuming()->shouldBeCalledTimes(4)->willReturn(true, true, true, false);
        $channel->wait()->shouldBeCalledTimes(3)->will(function () {
            usleep(10);
        });

        $channel->basic_consume('foo', Argument::cetera())->shouldBeCalledOnce();

        $consumer = new Consumer($connection->reveal(), $queueOptions, fn () => null);
        $consumer->setAutoSetupFabricEnabled(false);
        $consumer->start();
    }

    public function testConsume(): void
    {
        $channel = $this->prophesize(AMQPChannel::class);
        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $queueOptions = QueueOptions::fromArray(['name' => 'foo']);

        $channel->is_consuming()->shouldBeCalledTimes(4)->willReturn(true, true, true, false);
        $channel->wait(null, false, 0)->shouldBeCalledTimes(3)->will(function () {
            usleep(10);
        });

        $channel->basic_consume('foo', Argument::cetera())->shouldBeCalledOnce();

        $consumer = new Consumer($connection->reveal(), $queueOptions, fn () => null);
        $consumer->setAutoSetupFabricEnabled(false);
        $consumer->consume();
    }

    public function testConsumeWithStop(): void
    {
        $channel = $this->prophesize(AMQPChannel::class);
        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $queueOptions = QueueOptions::fromArray(['name' => 'foo']);
        $consumer = new Consumer($connection->reveal(), $queueOptions, fn () => null);
        $consumer->setAutoSetupFabricEnabled(false);
        $consumer->setConsumerTag('consumer_tag');

        $channel->is_consuming()->shouldBeCalled()->willReturn(true, true, false);
        $channel->wait(null, false, 0)->shouldBeCalledTimes(2)->will(function () use ($consumer) {
            usleep(10);
            $consumer->forceStopConsumer();
        });

        $channel->basic_cancel('consumer_tag')->shouldBeCalledOnce();
        $channel->basic_consume('foo', Argument::cetera())->shouldBeCalledOnce();

        $consumer->consume();
    }

    public function processMessageProvider(): array
    {
        return [
            [
                Consumer::MSG_ACK,
                'basic_ack',
                [
                    'foo',
                ],
            ],
            [
                Consumer::MSG_REJECT,
                'basic_reject',
                [
                    'foo',
                    false,
                ],
            ],
            [
                Consumer::MSG_REJECT_REQUEUE,
                'basic_reject',
                [
                    'foo',
                    true,
                ],
            ],
            [
                Consumer::MSG_SINGLE_NACK_REQUEUE,
                'basic_nack',
                [
                    'foo',
                    false,
                    true,
                ],
            ],
        ];
    }
}
