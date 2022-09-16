<?php

declare(strict_types=1);

namespace RabbitMqModule;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\Options\Queue;
use function count;
use Laminas\Serializer\Adapter\AdapterInterface as SerializerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RpcClient extends BaseAmqp
{
    protected int $requests = 0;

    /** @var array<string, mixed> */
    protected array $replies = [];

    protected int $timeout = 0;

    protected ?string $queueName = null;

    protected ?SerializerInterface $serializer = null;

    /**
     * @param mixed $body
     * @param string $server
     * @param string $requestId
     * @param string $routingKey
     * @param int $expiration
     * @return void
     */
    public function addRequest($body, string $server, string $requestId, string $routingKey = '', int $expiration = 0): void
    {
        if ($this->serializer) {
            $body = $this->serializer->serialize($body);
        }

        if (! is_string($body)) {
            throw new \InvalidArgumentException('The body must be a string');
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
        if (null !== $this->queueName) {
            return $this->queueName;
        }

        /** @psalm-var non-empty-list<string> $result */
        $result = $this->getChannel()->queue_declare('', false, false, true, false);

        [$queueName] = $result;

        return $this->queueName = $queueName;
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

    public function processMessage(AMQPMessage $message): void
    {
        /** @var string $correlationId */
        $correlationId = $message->get('correlation_id');
        /** @var mixed $messageBody */
        $messageBody = $this->serializer ? $this->serializer->unserialize($message->body) : $message->body;
        $this->replies[$correlationId] = $messageBody;
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
