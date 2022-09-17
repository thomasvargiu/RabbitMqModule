<?php

namespace RabbitMqModule;

use Laminas\Serializer\Serializer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;

class RpcClientTest extends TestCase
{
    public function testAddRequestAndGetReplies(): void
    {
        $body = 'body';
        $server = 'server';
        $requestId = 'request_1';
        $routingKey = '';
        $expiration = 2;

        $serializer = Serializer::factory('json');

        $channel = $this->prophesize(AMQPChannel::class);

        $connection = $this->prophesize(AbstractConnection::class);
        $connection->channel()->willReturn($channel->reveal());

        $channel->queue_declare('', false, false, true, false)
            ->shouldBeCalledOnce()
            ->willReturn(['queue-name', null, null]);

        $channel->basic_publish(
            Argument::that(fn (AMQPMessage $a) => $a->body === $serializer->serialize($body)
            && $a->get('reply_to') === 'queue-name'
            && $a->get('correlation_id') === $requestId
            && $a->get('delivery_mode') === 1
            && $a->get('expiration') === 2000),
            $server,
            $routingKey
        )->shouldBeCalledOnce();

        $rpcClient = new RpcClient($connection->reveal());
        $rpcClient->setSerializer($serializer);

        $rpcClient->addRequest($body, $server, $requestId, $routingKey, $expiration);

        $message = new AMQPMessage();
        $message->body = $serializer->serialize('response');
        $message->set('correlation_id', $requestId);

        $channel->basic_consume(
            'queue-name',
            '',
            false,
            true,
            false,
            false,
            Argument::type('callable')
        )
            ->shouldBeCalledOnce()
            ->willReturn('consumer_tag');

        $channel->wait(null, false, 2)
            ->shouldBeCalled()
            ->will(function ($a) use ($rpcClient, $message) {
                $rpcClient->processMessage($message);

                return null === $a;
            });

        $channel->basic_cancel('consumer_tag')->shouldBeCalledOnce();

        $replies = $rpcClient->getReplies();

        static::assertIsArray($replies);
        static::assertCount(1, $replies);
        static::assertArrayHasKey($requestId, $replies);
        static::assertEquals('response', $replies[$requestId]);
    }
}
