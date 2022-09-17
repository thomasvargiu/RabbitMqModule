<?php

declare(strict_types=1);

namespace RabbitMqModule;

use Laminas\Serializer\Adapter\AdapterInterface as SerializerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RpcServer extends Consumer
{
    protected ?SerializerInterface $serializer = null;

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function processMessage(AMQPMessage $message): void
    {
        $channel = $this->getChannel();
        $channel->basic_ack($message->getDeliveryTag());
        $result = ($this->getCallback())($message);
        if ($this->serializer) {
            $result = $this->serializer->serialize($result);
        }
        /** @var string|null $replyTo */
        $replyTo = $message->has('reply_to') ? $message->get('reply_to') : null;
        if ($replyTo) {
            /** @var string $correlationId */
            $correlationId = $message->get('correlation_id');
            $this->sendReply((string) $result, $replyTo, $correlationId);
        }
        $this->maybeStopConsumer();
    }

    protected function sendReply(string $result, string $client, string $correlationId): void
    {
        $reply = new AMQPMessage($result, ['content_type' => 'text/plain', 'correlation_id' => $correlationId]);
        $this->getChannel()->basic_publish($reply, '', $client);
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function getSerializer(): ?SerializerInterface
    {
        return $this->serializer;
    }

    public function setSerializer(SerializerInterface $serializer = null): void
    {
        $this->serializer = $serializer;
    }
}
