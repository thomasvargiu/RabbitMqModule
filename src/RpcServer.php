<?php

declare(strict_types=1);

namespace RabbitMqModule;

use function call_user_func;
use Laminas\Serializer\Adapter\AdapterInterface as SerializerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class RpcServer extends Consumer
{
    /** @var SerializerInterface|null */
    protected $serializer;

    /**
     * @throws \Laminas\Serializer\Exception\ExceptionInterface
     */
    public function processMessage(AMQPMessage $message): void
    {
        /** @var \PhpAmqpLib\Channel\AMQPChannel $channel */
        $channel = $message->delivery_info['channel'];
        $channel->basic_ack($message->delivery_info['delivery_tag']);
        $result = call_user_func($this->getCallback(), $message);
        if ($this->serializer) {
            $result = $this->serializer->serialize($result);
        }
        $this->sendReply($result, (string) $message->get('reply_to'), $message->get('correlation_id'));
        $this->maybeStopConsumer();
    }

    /**
     * @param string $correlationId
     */
    protected function sendReply($result, string $client, $correlationId): void
    {
        $reply = new AMQPMessage($result, ['content_type' => 'text/plain', 'correlation_id' => $correlationId]);
        $this->getChannel()->basic_publish($reply, '', $client);
    }

    /**
     * Get the serializer.
     *
     * @return SerializerInterface
     */
    public function getSerializer(): ?SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * Set the serializer.
     */
    public function setSerializer(SerializerInterface $serializer = null): void
    {
        $this->serializer = $serializer;
    }
}
