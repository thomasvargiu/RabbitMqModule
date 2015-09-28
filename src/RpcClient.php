<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;
use Zend\Serializer\Adapter\AdapterInterface as SerializerInterface;

class RpcClient extends BaseAmqp
{
    /**
     * @var int
     */
    protected $requests = 0;
    /**
     * @var array
     */
    protected $replies = [];
    /**
     * @var int
     */
    protected $timeout = 0;
    /**
     * @var string
     */
    protected $queueName;
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param mixed  $body
     * @param string $server
     * @param mixed  $requestId
     * @param string $routingKey
     * @param int    $expiration
     */
    public function addRequest($body, $server, $requestId, $routingKey = '', $expiration = 0)
    {
        if ($this->serializer) {
            $body = $this->serializer->serialize($body);
        }
        $msg = new AMQPMessage($body, [
            'content_type' => 'text/plain',
            'reply_to' => $this->getQueueName(),
            'delivery_mode' => 1, // non durable
            'expiration' => $expiration * 1000,
            'correlation_id' => $requestId,
        ]);
        $this->getChannel()->basic_publish($msg, $server, $routingKey);

        ++$this->requests;

        if ($expiration > $this->timeout) {
            $this->timeout = $expiration;
        }
    }

    /**
     * @return string
     */
    protected function getQueueName()
    {
        if (null === $this->queueName) {
            list($this->queueName) = $this->getChannel()->queue_declare('', false, false, true, false);
        }

        return $this->queueName;
    }

    /**
     * @return array
     */
    public function getReplies()
    {
        $this->replies = [];
        $consumer_tag = $this->getChannel()
            ->basic_consume($this->getQueueName(), '', false, true, false, false, [$this, 'processMessage']);
        while (count($this->replies) < $this->requests) {
            $this->getChannel()->wait(null, false, $this->timeout);
        }
        $this->getChannel()->basic_cancel($consumer_tag);
        $this->requests = 0;
        $this->timeout = 0;

        return $this->replies;
    }

    /**
     * @param AMQPMessage $message
     */
    public function processMessage(AMQPMessage $message)
    {
        $messageBody = $message->body;
        if ($this->serializer) {
            $messageBody = $this->serializer->unserialize($messageBody);
        }
        $this->replies[$message->get('correlation_id')] = $messageBody;
    }

    /**
     * @param SerializerInterface|null $serializer
     */
    public function setSerializer(SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }
}
