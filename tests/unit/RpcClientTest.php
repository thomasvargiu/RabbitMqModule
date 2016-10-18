<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;
use Zend\Serializer\Serializer;

class RpcClientTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRequestAndGetReplies()
    {
        $body = 'body';
        $server = 'server';
        $requestId = 'request_1';
        $routingKey = '';
        $expiration = 2;

        $serializer = Serializer::factory('json');

        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        $channel->expects(static::once())
            ->method('queue_declare')
            ->with('', false, false, true, false)
            ->willReturn(['queue-name', null, null]);

        $channel->expects(static::once())
            ->method('basic_publish')
            ->with(
                static::callback(function (AMQPMessage $a) use ($body, $requestId, $serializer) {
                    return $a->body === $serializer->serialize($body)
                        && $a->get('reply_to') === 'queue-name'
                        && $a->get('correlation_id') === $requestId
                        && $a->get('delivery_mode') === 1
                        && $a->get('expiration') === 2000;
                }),
                $server,
                $routingKey
            );

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $rpcClient = new RpcClient($connection, $channel);
        $rpcClient->setSerializer($serializer);

        $rpcClient->addRequest($body, $server, $requestId, $routingKey, $expiration);

        $message = new AMQPMessage();
        $message->body = $serializer->serialize('response');
        $message->set('correlation_id', $requestId);

        $channel->expects(static::once())
            ->method('basic_consume')
            ->with('queue-name', '', false, true, false, false, static::callback(function ($a) {
                return is_callable($a);
            }))
            ->willReturn('consumer_tag');

        $channel->expects(static::once())
            ->method('wait')
            ->with(
                static::callback(function ($a) use ($rpcClient, $message) {
                    $rpcClient->processMessage($message);

                    return is_null($a);
                }),
                false,
                2
            );

        $channel->expects(static::once())
            ->method('basic_cancel')
            ->with('consumer_tag');

        $replies = $rpcClient->getReplies();

        static::assertInternalType('array', $replies);
        static::assertCount(1, $replies);
        static::assertArrayHasKey($requestId, $replies);
        static::assertEquals('response', $replies[$requestId]);
    }
}
