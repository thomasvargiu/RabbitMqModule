<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * Interface ConsumerInterface.
 *
 * @deprecated Use an invokable class instead
 */
interface ConsumerInterface
{
    /**
     * Flag for message ack.
     *
     * @deprecated Use {@see Consumer::MSG_ACK} instead.
     */
    public const MSG_ACK = Consumer::MSG_ACK;

    /**
     * Flag single for message nack and requeue.
     *
     * @deprecated Use {@see Consumer::MSG_SINGLE_NACK_REQUEUE} instead.
     */
    public const MSG_SINGLE_NACK_REQUEUE = Consumer::MSG_SINGLE_NACK_REQUEUE;

    /**
     * Flag for reject and requeue.
     *
     * @deprecated Use {@see Consumer::MSG_REJECT_REQUEUE} instead.
     */
    public const MSG_REJECT_REQUEUE = Consumer::MSG_REJECT_REQUEUE;

    /**
     * Flag for reject and drop.
     *
     * @deprecated Use {@see Consumer::MSG_REJECT} instead.
     */
    public const MSG_REJECT = Consumer::MSG_REJECT;

    /**
     * @deprecated Use an invokable class instead.
     */
    public function execute(AMQPMessage $message): ?int;
}
