<?php

declare(strict_types=1);

namespace RabbitMqModule;

use function count;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Wire\AMQPTable;
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
     * @var AMQPChannel|null
     */
    private $channel;

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
    public function getConnection(): AbstractConnection
    {
        return $this->connection;
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel
    {
        if (! $this->channel) {
            $this->channel = $this->getConnection()->channel();
        }

        return $this->channel;
    }

    /**
     * @param AMQPChannel $channel
     */
    public function setChannel(AMQPChannel $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * @return null|QueueOptions
     */
    public function getQueueOptions(): ?QueueOptions
    {
        return $this->queueOptions;
    }

    /**
     * @param QueueOptions $queueOptions
     */
    public function setQueueOptions(QueueOptions $queueOptions): void
    {
        $this->queueOptions = $queueOptions;
    }

    /**
     * @return ExchangeOptions
     */
    public function getExchangeOptions(): ExchangeOptions
    {
        return $this->exchangeOptions;
    }

    /**
     * @param ExchangeOptions $exchangeOptions
     */
    public function setExchangeOptions(ExchangeOptions $exchangeOptions): void
    {
        $this->exchangeOptions = $exchangeOptions;
    }

    /**
     * @return bool
     */
    public function isAutoSetupFabricEnabled(): bool
    {
        return $this->autoSetupFabricEnabled;
    }

    /**
     * @param bool $autoSetupFabricEnabled
     */
    public function setAutoSetupFabricEnabled(bool $autoSetupFabricEnabled): void
    {
        $this->autoSetupFabricEnabled = $autoSetupFabricEnabled;
    }

    /**
     * Declare Exchange
     *
     * @param ExchangeOptions $options
     */
    protected function declareExchange(ExchangeOptions $options = null): void
    {
        if (! $options) {
            $options = $this->getExchangeOptions();
        }

        if (! $options->isDeclare()) {
            return;
        }

        $arguments = $options->getArguments();

        $this->getChannel()->exchange_declare(
            $options->getName(),
            $options->getType(),
            $options->isPassive(),
            $options->isDurable(),
            $options->isAutoDelete(),
            $options->isInternal(),
            $options->isNoWait(),
            $arguments ? new AMQPTable($arguments) : [],
            $options->getTicket()
        );

        $binds = $options->getExchangeBinds();
        foreach ($binds as $bind) {
            $this->declareExchange($bind->getExchange());
            $routingKeys = $bind->getRoutingKeys();
            if (! count($routingKeys)) {
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
    }

    /**
     * Declare queue
     */
    protected function declareQueue(): void
    {
        $queueOptions = $this->getQueueOptions();

        if (! $queueOptions || null === $queueOptions->getName()) {
            return;
        }

        $exchangeOptions = $this->getExchangeOptions();
        $arguments = $queueOptions->getArguments();

        [$queueName] = $this->getChannel()->queue_declare(
            $queueOptions->getName(),
            $queueOptions->isPassive(),
            $queueOptions->isDurable(),
            $queueOptions->isExclusive(),
            $queueOptions->isAutoDelete(),
            $queueOptions->isNoWait(),
            $arguments ? new AMQPTable($arguments) : [],
            $queueOptions->getTicket()
        );

        $routingKeys = $queueOptions->getRoutingKeys();
        if (! count($routingKeys)) {
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
    }

    /**
     * Declare queues and exchanges
     */
    public function setupFabric(): void
    {
        if (! $this->exchangeDeclared) {
            $this->declareExchange();
        }

        $queueOptions = $this->getQueueOptions();

        if (! $this->queueDeclared && $queueOptions) {
            $this->declareQueue();
        }
    }

    /**
     * Reconnect
     */
    public function reconnect(): void
    {
        $this->channel = null;
        $this->getConnection()->reconnect();
    }
}
