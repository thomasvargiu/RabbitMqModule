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
    protected AbstractConnection $connection;

    private ?AMQPChannel $channel = null;

    protected ?QueueOptions $queueOptions = null;

    protected ?ExchangeOptions $exchangeOptions = null;

    protected bool $autoSetupFabricEnabled = true;

    protected bool $exchangeDeclared = false;

    protected bool $queueDeclared = false;

    public function __construct(AbstractConnection $connection, AMQPChannel $channel = null)
    {
        $this->connection = $connection;
        $this->channel = $channel;
    }

    public function getConnection(): AbstractConnection
    {
        return $this->connection;
    }

    public function getChannel(): AMQPChannel
    {
        if (! $this->channel) {
            $this->channel = $this->getConnection()->channel();
        }

        return $this->channel;
    }

    public function setChannel(AMQPChannel $channel): void
    {
        $this->channel = $channel;
    }

    public function getQueueOptions(): ?QueueOptions
    {
        return $this->queueOptions;
    }

    public function setQueueOptions(?QueueOptions $queueOptions): void
    {
        $this->queueOptions = $queueOptions;
    }

    public function getExchangeOptions(): ?ExchangeOptions
    {
        return $this->exchangeOptions;
    }

    public function setExchangeOptions(?ExchangeOptions $exchangeOptions): void
    {
        $this->exchangeOptions = $exchangeOptions;
    }

    public function isAutoSetupFabricEnabled(): bool
    {
        return $this->autoSetupFabricEnabled;
    }

    public function setAutoSetupFabricEnabled(bool $autoSetupFabricEnabled): void
    {
        $this->autoSetupFabricEnabled = $autoSetupFabricEnabled;
    }

    /**
     * Declare Exchange
     */
    protected function declareExchange(?ExchangeOptions $options = null): void
    {
        if (! $options) {
            $options = $this->getExchangeOptions();
        }

        if (! $options || ! $options->isDeclare()) {
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
            false,
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

        if (! $queueOptions || '' === $queueOptions->getName()) {
            return;
        }

        $exchangeOptions = $this->getExchangeOptions();
        $arguments = $queueOptions->getArguments();

        /** @psalm-var non-empty-list<string> $result */
        $result = $this->getChannel()->queue_declare(
            $queueOptions->getName(),
            $queueOptions->isPassive(),
            $queueOptions->isDurable(),
            $queueOptions->isExclusive(),
            $queueOptions->isAutoDelete(),
            false,
            $arguments ? new AMQPTable($arguments) : [],
            $queueOptions->getTicket()
        );

        [$queueName] = $result;

        if (null === $exchangeOptions) {
            throw new \RuntimeException('Unable to create queue bindings: no exchange configuration provided');
        }

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
