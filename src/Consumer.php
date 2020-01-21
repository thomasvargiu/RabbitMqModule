<?php

declare(strict_types=1);

namespace RabbitMqModule;

use function call_user_func;
use function count;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer extends BaseConsumer
{
    /**
     * Purge the queue.
     */
    public function purgeQueue(): void
    {
        $this->getChannel()->queue_purge($this->getQueueOptions()->getName(), true);
    }

    /**
     * Consume the message.
     */
    public function consume(): void
    {
        $this->setupConsumer();
        while (count($this->getChannel()->callbacks)) {
            $this->maybeStopConsumer();
            $this->getChannel()->wait(null, false, $this->getIdleTimeout());
        }
    }

    /**
     * @param AMQPMessage $message
     */
    public function processMessage(AMQPMessage $message): void
    {
        $this->getEventManager()->trigger(__FUNCTION__ . '.pre', $this, compact('message'));

        $processFlag = call_user_func($this->getCallback(), $message);
        $this->handleProcessMessage($message, $processFlag);

        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, compact('message'));
    }

    /**
     * @param AMQPMessage $msg
     * @param bool|int $processFlag
     */
    protected function handleProcessMessage(AMQPMessage $msg, $processFlag): void
    {
        /** @var AMQPChannel $channel */
        $channel = $msg->delivery_info['channel'];
        /** @var string $deliveryTag */
        $deliveryTag = $msg->delivery_info['delivery_tag'];
        if ($processFlag === ConsumerInterface::MSG_REJECT_REQUEUE || false === $processFlag) {
            // Reject and requeue message to RabbitMQ
            $channel->basic_reject($deliveryTag, true);
        } elseif ($processFlag === ConsumerInterface::MSG_SINGLE_NACK_REQUEUE) {
            // NACK and requeue message to RabbitMQ
            $channel->basic_nack($deliveryTag, false, true);
        } elseif ($processFlag === ConsumerInterface::MSG_REJECT) {
            // Reject and drop
            $channel->basic_reject($deliveryTag, false);
        } else {
            // Remove message from queue only if callback return not false
            $channel->basic_ack($deliveryTag);
        }
        $this->maybeStopConsumer();
    }
}
