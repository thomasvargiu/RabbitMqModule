<?php

namespace RabbitMqModule;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\Options\Exchange as ExchangeOptions;
use RabbitMqModule\Options\Queue as QueueOptions;
use RabbitMqModule\Service\SetupFabricAwareInterface;

abstract class BaseAmqp implements SetupFabricAwareInterface
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
     * @var QueueOptions
     */
    protected $queueOptions;
    /**
     * @var ExchangeOptions
     */
    protected $exchangeOptions;
    /**
     * @var bool
     */
    protected $autoSetupFabricEnabled = true;
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
     *
     * @return $this
     */
    public function setChannel(AMQPChannel $channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return QueueOptions
     */
    public function getQueueOptions()
    {
        return $this->queueOptions;
    }

    /**
     * @param QueueOptions $queueOptions
     *
     * @return $this
     */
    public function setQueueOptions(QueueOptions $queueOptions)
    {
        $this->queueOptions = $queueOptions;

        return $this;
    }

    /**
     * @return ExchangeOptions
     */
    public function getExchangeOptions()
    {
        return $this->exchangeOptions;
    }

    /**
     * @param ExchangeOptions $exchangeOptions
     *
     * @return $this
     */
    public function setExchangeOptions(ExchangeOptions $exchangeOptions)
    {
        $this->exchangeOptions = $exchangeOptions;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoSetupFabricEnabled()
    {
        return $this->autoSetupFabricEnabled;
    }

    /**
     * @param bool $autoSetupFabricEnabled
     *
     * @return $this
     */
    public function setAutoSetupFabricEnabled($autoSetupFabricEnabled)
    {
        $this->autoSetupFabricEnabled = $autoSetupFabricEnabled;

        return $this;
    }

    /**
     * @param ExchangeOptions $options
     *
     * @return $this
     */
    protected function declareExchange(ExchangeOptions $options = null)
    {
        if (!$options) {
            $options = $this->getExchangeOptions();
        }

        if (!$options->isDeclare()) {
            // @codeCoverageIgnoreStart
            return $this;
            // @codeCoverageIgnoreEnd
        }

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

        $binds = $options->getExchangeBinds();
        foreach ($binds as $bind) {
            $this->declareExchange($bind->getExchange());
            $routingKeys = $bind->getRoutingKeys();
            if (!count($routingKeys)) {
                $routingKeys = [''];
            }
            foreach ($routingKeys as $routingKey) {
                $this->getChannel()->exchange_bind(
                    $options->getName(),
                    $bind->getExchange()->getName(),
                    $routingKey
                );
            }
        }

        $this->exchangeDeclared = true;

        return $this;
    }

    /**
     * @return $this
     */
    protected function declareQueue()
    {
        $queueOptions = $this->getQueueOptions();

        if (!$queueOptions || null === $queueOptions->getName()) {
            // @codeCoverageIgnoreStart
            return $this;
            // @codeCoverageIgnoreEnd
        }

        $exchangeOptions = $this->getExchangeOptions();

        list($queueName) = $this->getChannel()->queue_declare(
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
        if (!count($routingKeys)) {
            $routingKeys = [''];
        }
        foreach ($routingKeys as $routingKey) {
            $this->getChannel()->queue_bind(
                $queueName,
                $exchangeOptions->getName(),
                $routingKey
            );
        }

        $this->queueDeclared = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function setupFabric()
    {
        if (!$this->exchangeDeclared) {
            $this->declareExchange();
        }

        $queueOptions = $this->getQueueOptions();

        if (!$this->queueDeclared && $queueOptions) {
            $this->declareQueue();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function reconnect()
    {
        if (!$this->getConnection()->isConnected()) {
            // @codeCoverageIgnoreStart
            return $this;
            // @codeCoverageIgnoreEnd
        }
        $this->channel = null;
        $this->getConnection()->reconnect();

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
