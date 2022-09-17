<?php

declare(strict_types=1);

namespace RabbitMqModule;

use function call_user_func;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer extends BaseConsumer
{
    /**
     * Flag for message ack.
     */
    public const MSG_ACK = 1;

    /**
     * Flag single for message nack and requeue.
     */
    public const MSG_SINGLE_NACK_REQUEUE = 2;

    /**
     * Flag for reject and requeue.
     */
    public const MSG_REJECT_REQUEUE = 0;

    /**
     * Flag for reject and drop.
     */
    public const MSG_REJECT = -1;

    /**
     * Purge the queue.
     */
    public function purgeQueue(): void
    {
        $this->getChannel()->queue_purge($this->queueName, true);
    }

    /**
     * Consume the message.
     */
    public function consume(): void
    {
        $this->setupConsumer();
        while ($this->getChannel()->is_consuming()) {
            $this->maybeStopConsumer();
            $this->getChannel()->wait(null, false, $this->getIdleTimeout());
        }
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function processMessage(AMQPMessage $message): void
    {
        $processFlag = call_user_func($this->getCallback(), $message);
        $this->handleProcessMessage($message, $processFlag);
    }

    /**
     * @param mixed $processFlag
     */
    protected function handleProcessMessage(AMQPMessage $msg, $processFlag): void
    {
        /* @psalm-suppress DocblockTypeContradiction */
        if (null !== $processFlag && ! is_int($processFlag)) {
            trigger_error(
                'Consumer handler should return an integer or void/null. Returning a different type is deprecated',
                E_USER_DEPRECATED
            );
        }

        $channel = $msg->getChannel() ?: $this->getChannel();
        $deliveryTag = $msg->getDeliveryTag();
        if ($processFlag === self::MSG_REJECT_REQUEUE || false === $processFlag) {
            // Reject and requeue message to RabbitMQ
            $channel->basic_reject($deliveryTag, true);
        } elseif ($processFlag === self::MSG_SINGLE_NACK_REQUEUE) {
            // NACK and requeue message to RabbitMQ
            $channel->basic_nack($deliveryTag, false, true);
        } elseif ($processFlag === self::MSG_REJECT) {
            // Reject and drop
            $channel->basic_reject($deliveryTag, false);
        } else {
            // Remove message from queue only if callback return not false
            $channel->basic_ack($deliveryTag);
        }
        $this->maybeStopConsumer();
    }
}
