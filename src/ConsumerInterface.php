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
     * @deprecated use {@see Consumer::MSG_ACK} instead
     */
    public const MSG_ACK = Consumer::MSG_ACK;

    /**
     * Flag single for message nack and requeue.
     *
     * @deprecated use {@see Consumer::MSG_SINGLE_NACK_REQUEUE} instead
     */
    public const MSG_SINGLE_NACK_REQUEUE = Consumer::MSG_SINGLE_NACK_REQUEUE;

    /**
     * Flag for reject and requeue.
     *
     * @deprecated use {@see Consumer::MSG_REJECT_REQUEUE} instead
     */
    public const MSG_REJECT_REQUEUE = Consumer::MSG_REJECT_REQUEUE;

    /**
     * Flag for reject and drop.
     *
     * @deprecated use {@see Consumer::MSG_REJECT} instead
     */
    public const MSG_REJECT = Consumer::MSG_REJECT;

    /**
     * @deprecated use an invokable class instead
     */
    public function execute(AMQPMessage $message): ?int;
}
