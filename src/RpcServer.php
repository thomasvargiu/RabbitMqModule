<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;
use Zend\Serializer\Adapter\AdapterInterface as SerializerInterface;

class RpcServer extends Consumer
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param AMQPMessage $message
     */
    public function processMessage(AMQPMessage $message)
    {
        /** @var \PhpAmqpLib\Channel\AMQPChannel $channel */
        $channel = $message->delivery_info['channel'];
        $channel->basic_ack($message->delivery_info['delivery_tag']);
        $result = call_user_func($this->getCallback(), $message);
        if ($this->serializer) {
            $result = $this->serializer->serialize($result);
        }
        $this->sendReply($result, $message->get('reply_to'), $message->get('correlation_id'));
        $this->maybeStopConsumer();
    }

    /**
     * @param mixed  $result
     * @param string $client
     * @param string $correlationId
     */
    protected function sendReply($result, $client, $correlationId)
    {
        $reply = new AMQPMessage($result, ['content_type' => 'text/plain', 'correlation_id' => $correlationId]);
        $this->getChannel()->basic_publish($reply, '', $client);
    }

    /**
     * Get the serializer.
     *
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Set the serializer.
     *
     * @param SerializerInterface $serializer
     *
     * @return $this
     */
    public function setSerializer(SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer;

        return $this;
    }
}
