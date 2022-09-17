<?php

namespace RabbitMqModule;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;
use RabbitMqModule\Options\Exchange as ExchangeOptions;
use RabbitMqModule\Options\Queue as QueueOptions;

class ProducerTest extends TestCase
{
    public function testProperties(): void
    {
        $connection = $this->prophesize(AbstractConnection::class);

        $exchangeOptions = new ExchangeOptions();
        $producer = new Producer($connection->reveal(), $exchangeOptions);

        static::assertSame($connection->reveal(), $producer->getConnection());
        static::assertEquals('text/plain', $producer->getContentType());
        static::assertEquals(2, $producer->getDeliveryMode());

        $queueOptions = new QueueOptions();

        $producer->setDeliveryMode(-1);
        $producer->setContentType('foo');
        $producer->setQueueOptions($queueOptions);
        $producer->setExchangeOptions($exchangeOptions);
        $producer->setAutoSetupFabricEnabled(false);

        static::assertEquals('foo', $producer->getContentType());
        static::assertEquals(-1, $producer->getDeliveryMode());
        static::assertSame($queueOptions, $producer->getQueueOptions());
        static::assertSame($exchangeOptions, $producer->getExchangeOptions());
        static::assertFalse($producer->isAutoSetupFabricEnabled());
    }

    public function testSetupFabric(): void
    {
        $channel = $this->prophesize(AMQPChannel::class);

        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $exchangeOptions = ExchangeOptions::fromArray(['name' => 'exchange-name']);
        $queueOptions = new QueueOptions();

        $producer = new Producer($connection->reveal(), $exchangeOptions);

        $queueOptions->setName('foo');

        $producer->setQueueOptions($queueOptions);
        $producer->setExchangeOptions($exchangeOptions);

        $channel->exchange_declare('exchange-name', Argument::cetera())->shouldBeCalledOnce();
        $channel->queue_declare('foo', Argument::cetera())->shouldBeCalledOnce()->willReturn(['foo']);
        $channel->queue_bind('foo', 'exchange-name', '')->shouldBeCalledOnce();

        $producer->setupFabric();
    }

    public function testPublish(): void
    {
        $channel = $this->prophesize(AMQPChannel::class);

        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $exchangeOptions = ExchangeOptions::fromArray(['name' => 'foo']);
        $queueOptions = QueueOptions::fromArray(['name' => 'foo']);

        $producer = new Producer($connection->reveal(), $exchangeOptions);
        $producer->setQueueOptions($queueOptions);

        $connection->isConnected()->willReturn(true);
        $connection->reconnect()->shouldNotBeCalled();

        $channel->exchange_declare(Argument::cetera())->shouldBeCalledOnce();
        $channel->queue_declare(Argument::cetera())->shouldBeCalledOnce()->willReturn(['foo']);
        $channel->queue_bind(Argument::cetera())->shouldBeCalledOnce();

        $channel->basic_publish(Argument::that(fn (AMQPMessage $message) => $message->body === 'test-body' && $message->get_properties() === [
            'content_type' => 'foo/bar',
            'delivery_mode' => 2,
        ]), 'foo', 'test-key')->shouldBeCalledOnce();

        $producer->publish('test-body', 'test-key', ['content_type' => 'foo/bar']);
    }

    public function testShouldReconnectOnPublishWhenDisconnected(): void
    {
        $channel = $this->prophesize(AMQPChannel::class);

        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $exchangeOptions = ExchangeOptions::fromArray(['name' => 'foo']);
        $queueOptions = QueueOptions::fromArray(['name' => 'foo']);

        $producer = new Producer($connection->reveal(), $exchangeOptions);
        $producer->setQueueOptions($queueOptions);

        $connection->isConnected()->willReturn(false);
        $connection->reconnect()->shouldBeCalled();
        $connection->channel()->willReturn($channel->reveal());

        $channel->exchange_declare(Argument::cetera())->shouldBeCalledOnce();
        $channel->queue_declare(Argument::cetera())->shouldBeCalledOnce()->willReturn(['foo']);
        $channel->queue_bind(Argument::cetera())->shouldBeCalledOnce();

        $channel->basic_publish(Argument::that(fn (AMQPMessage $message) => $message->body === 'test-body' && $message->get_properties() === [
            'content_type' => 'foo/bar',
            'delivery_mode' => 2,
        ]), 'foo', 'test-key')->shouldBeCalledOnce();

        $producer->publish('test-body', 'test-key', ['content_type' => 'foo/bar']);
    }
}
