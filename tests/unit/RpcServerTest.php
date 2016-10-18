<?php

namespace RabbitMqModule;

use Zend\Serializer\Serializer;

class RpcServerTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessMessage()
    {
        $response = 'ciao';

        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        $message = new \PhpAmqpLib\Message\AMQPMessage('request', [
            'reply_to' => 'foo',
            'correlation_id' => 'bar',
        ]);

        $message->delivery_info = [
            'channel' => $channel,
            'delivery_tag' => 'foo',
        ];

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $rpcServer = new RpcServer($connection, $channel);
        $rpcServer->setCallback(function () use ($response) {
            return $response;
        });

        $channel->expects(static::once())->method('basic_publish')
            ->with(
                static::callback(function ($a) use ($response) {
                    return $a instanceof \PhpAmqpLib\Message\AMQPMessage
                        && $a->body === $response
                        && $a->get('correlation_id') === 'bar'
                        && $a->get('content_type') === 'text/plain';
                }),
                static::equalTo(''),
                static::equalTo('foo')
            );

        $rpcServer->processMessage($message);
    }

    public function testProcessMessageWithSerializer()
    {
        $response = ['response' => 'ciao'];

        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();

        $message = new \PhpAmqpLib\Message\AMQPMessage('request', [
            'reply_to' => 'foo',
            'correlation_id' => 'bar',
        ]);

        $message->delivery_info = [
            'channel' => $channel,
            'delivery_tag' => 'foo',
        ];

        $serializer = Serializer::factory('json');

        /* @var \PhpAmqpLib\Connection\AbstractConnection $connection */
        $rpcServer = new RpcServer($connection, $channel);
        $rpcServer->setSerializer($serializer);
        $rpcServer->setCallback(function () use ($response) {
            return $response;
        });

        $channel->expects(static::once())->method('basic_publish')
            ->with(
                static::callback(function ($a) use ($response) {
                    return $a instanceof \PhpAmqpLib\Message\AMQPMessage
                    && $a->body === '{"response":"ciao"}'
                    && $a->get('correlation_id') === 'bar'
                    && $a->get('content_type') === 'text/plain';
                }),
                static::equalTo(''),
                static::equalTo('foo')
            );

        $rpcServer->processMessage($message);
    }
}
