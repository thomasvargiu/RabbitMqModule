<?php

namespace RabbitMqModule\Service;

use RabbitMqModule\Options\Exchange as ExchangeOptions;
use RabbitMqModule\Options\Queue as QueueOptions;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMqService
{
    /**
     * @param AMQPChannel     $channel
     * @param ExchangeOptions $options
     * @return $this
     */
    public function declareExchange(AMQPChannel $channel, ExchangeOptions $options)
    {
        $channel->exchange_declare(
            $options->getName(),
            $options->getType(),
            $options->isPassive(),
            $options->isDurable(),
            $options->isAutoDelete(),
            $options->isInternal(),
            $options->isNoWait(),
            $options->getArguments(),
            $options->getTicket()
        );

        return $this;
    }

    /**
     * @param AMQPChannel     $channel
     * @param ExchangeOptions $exchangeOptions
     * @param QueueOptions    $queueOptions
     * @return $this
     */
    public function declareQueue(AMQPChannel $channel, ExchangeOptions $exchangeOptions, QueueOptions $queueOptions)
    {
        list ($queueName, ,) = $channel->queue_declare(
            $queueOptions->getName(),
            $queueOptions->isPassive(),
            $queueOptions->isDurable(),
            $queueOptions->isExclusive(),
            $queueOptions->isAutoDelete(),
            $queueOptions->isNoWait(),
            $queueOptions->getArguments(),
            $queueOptions->getTicket()
        );

        $routingKeys = $queueOptions->getRoutingKeys();
        if (count($routingKeys)) {
            foreach ($routingKeys as $routingKey) {
                $channel->queue_bind(
                    $queueName,
                    $exchangeOptions->getName(),
                    $routingKey
                );
            }
        } else {
            $channel->queue_bind(
                $queueName,
                $exchangeOptions->getName(),
                ''
            );
        }

        return $this;
    }
}
