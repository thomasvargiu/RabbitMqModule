<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * Interface ConsumerInterface.
 */
interface ConsumerInterface
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
     * @param AMQPMessage $message
     *
     * @return int|null
     */
    public function execute(AMQPMessage $message): ?int;
}
