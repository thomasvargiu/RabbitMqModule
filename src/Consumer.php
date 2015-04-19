<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;

class Consumer extends BaseConsumer
{

    /**
     * Purge the queue
     *
     * @return $this
     */
    public function purgeQueue()
    {
        $this->getChannel()->queue_purge($this->getOptions()->getQueue()->getName(), true);

        return $this;
    }

    public function processMessage(AMQPMessage $msg)
    {
        $processFlag = call_user_func($this->getOptions()->getCallback(), $msg);
        $this->handleProcessMessage($msg, $processFlag);
    }

    protected function handleProcessMessage(AMQPMessage $msg, $processFlag)
    {
        if ($processFlag === ConsumerInterface::MSG_REJECT_REQUEUE || false === $processFlag) {
            // Reject and requeue message to RabbitMQ
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], true);
        } elseif ($processFlag === ConsumerInterface::MSG_SINGLE_NACK_REQUEUE) {
            // NACK and requeue message to RabbitMQ
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag'], false, true);
        } elseif ($processFlag === ConsumerInterface::MSG_REJECT) {
            // Reject and drop
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], false);
        } else {
            // Remove message from queue only if callback return not false
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }
        $this->maybeStopConsumer();
    }

}
