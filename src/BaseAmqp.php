<?php

namespace RabbitMqModule;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\Options\Exchange as ExchangeOptions;
use RabbitMqModule\Options\Queue as QueueOptions;

abstract class BaseAmqp
{
    /**
     * @var AbstractConnection
     */
    protected $connection;
    /**
     * @var AMQPChannel
     */
    protected $channel;
    /**
     * @var bool
     */
    protected $exchangeDeclared = false;
    /**
     * @var bool
     */
    protected $queueDeclared = false;

    /**
     * @param AbstractConnection $connection
     * @param AMQPChannel        $channel
     */
    public function __construct(AbstractConnection $connection, AMQPChannel $channel = null)
    {
        $this->connection = $connection;
        $this->channel = $channel;
    }

    /**
     * @return AbstractConnection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel()
    {
        if (!$this->channel) {
            $this->channel = $this->getConnection()->channel();
        }
        return $this->channel;
    }

    /**
     * @param AMQPChannel $channel
     * @return $this
     */
    public function setChannel(AMQPChannel $channel)
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @param ExchangeOptions $options
     * @return $this
     */
    protected function declareExchange(ExchangeOptions $options)
    {
        $this->getChannel()->exchange_declare(
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
     * @param ExchangeOptions $exchangeOptions
     * @param QueueOptions    $queueOptions
     * @return $this
     */
    protected function declareQueue(ExchangeOptions $exchangeOptions, QueueOptions $queueOptions)
    {
        list ($queueName, ,) = $this->getChannel()->queue_declare(
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
                $this->getChannel()->queue_bind(
                    $queueName,
                    $exchangeOptions->getName(),
                    $routingKey
                );
            }
        } else {
            $this->getChannel()->queue_bind(
                $queueName,
                $exchangeOptions->getName(),
                ''
            );
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function reconnect()
    {
        if (!$this->getConnection()->isConnected()) {
            return $this;
        }
        $this->channel = null;
        $this->getConnection()->reconnect();

        return $this;
    }

    /**
     * @param ExchangeOptions $exchangeOptions
     * @param QueueOptions    $queueOptions
     * @return $this
     */
    protected function explicitSetupFabric(ExchangeOptions $exchangeOptions, QueueOptions $queueOptions = null)
    {
        $this->declareExchange($exchangeOptions);
        $this->declareQueue($exchangeOptions, $queueOptions);

        return $this;
    }

    public function __destruct()
    {
        if ($this->channel) {
            $this->channel->close();
        }

        if ($this->connection && $this->getConnection()->isConnected()) {
            $this->getConnection()->close();
        }
    }
}
