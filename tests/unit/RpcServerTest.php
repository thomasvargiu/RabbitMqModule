<?php

namespace RabbitMqModule;

use Laminas\Serializer\Serializer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;
use RabbitMqModule\Options\Queue as QueueOptions;

class RpcServerTest extends TestCase
{
    public function testProcessMessage(): void
    {
        $response = 'ciao';

        $channel = $this->prophesize(AMQPChannel::class);
        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $message = new AMQPMessage('request', [
            'reply_to' => 'foo',
            'correlation_id' => 'bar',
        ]);
        $message->setChannel($channel->reveal());
        $message->setDeliveryTag('foo');

        $queueOptions = QueueOptions::fromArray(['name' => 'foo']);

        $rpcServer = new RpcServer($connection->reveal(), $queueOptions, fn () => $response);

        $channel->basic_publish(
            Argument::that(fn (AMQPMessage $a) => $a->body === 'ciao'
                && $a->get('correlation_id') === 'bar'
                && $a->get('content_type') === 'text/plain'),
            '',
            'foo'
        )->shouldBeCalledOnce();
        $channel->basic_ack('foo')->shouldBeCalled();

        $rpcServer->processMessage($message);
    }

    public function testProcessMessageWithSerializer(): void
    {
        $response = ['response' => 'ciao'];

        $channel = $this->prophesize(AMQPChannel::class);
        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $message = new AMQPMessage('request', [
            'reply_to' => 'foo',
            'correlation_id' => 'bar',
        ]);
        $message->setChannel($channel->reveal());
        $message->setDeliveryTag('foo');

        $queueOptions = QueueOptions::fromArray(['name' => 'foo']);

        $rpcServer = new RpcServer($connection->reveal(), $queueOptions, fn () => $response);
        $rpcServer->setSerializer(Serializer::factory('json'));

        $channel->basic_publish(
            Argument::that(fn (AMQPMessage $a) => $a->body === '{"response":"ciao"}'
                && $a->get('correlation_id') === 'bar'
                && $a->get('content_type') === 'text/plain'),
            '',
            'foo'
        )->shouldBeCalledOnce();
        $channel->basic_ack('foo')->shouldBeCalled();

        $rpcServer->processMessage($message);
    }
}
