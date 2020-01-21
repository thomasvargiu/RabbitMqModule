<?php

declare(strict_types=1);

namespace RabbitMqModule;

use function count;
use Laminas\Serializer\Adapter\AdapterInterface as SerializerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RpcClient extends BaseAmqp
{
    /**
     * @var int
     */
    protected $requests = 0;

    /**
     * @var array<string, mixed>
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
     * @var SerializerInterface|null
     */
    protected $serializer;

    /**
     * @throws \Laminas\Serializer\Exception\ExceptionInterface
     */
    public function addRequest($body, string $server, $requestId, string $routingKey = '', int $expiration = 0): void
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

    protected function getQueueName(): string
    {
        if (null === $this->queueName) {
            [$this->queueName] = $this->getChannel()->queue_declare('', false, false, true, false);
        }

        return $this->queueName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getReplies(): array
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
     * @throws \Laminas\Serializer\Exception\ExceptionInterface
     */
    public function processMessage(AMQPMessage $message): void
    {
        $messageBody = $message->body;
        if ($this->serializer) {
            $messageBody = $this->serializer->unserialize($messageBody);
        }
        $this->replies[$message->get('correlation_id')] = $messageBody;
    }

    public function setSerializer(SerializerInterface $serializer = null): void
    {
        $this->serializer = $serializer;
    }

    public function getSerializer(): ?SerializerInterface
    {
        return $this->serializer;
    }
}
