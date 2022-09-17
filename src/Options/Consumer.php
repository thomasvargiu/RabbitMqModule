<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use InvalidArgumentException;
use function is_array;

/**
 * @psalm-import-type ExchangeOptions from Exchange
 * @psalm-import-type QueueOptions from Queue
 * @psalm-import-type QosOptions from Qos
 *
 * @psalm-type ConsumerOptions = array{
 *   description?: string,
 *   connection?: string,
 *   queue: QueueOptions|Queue,
 *   exchange?: ExchangeOptions|Exchange,
 *   callback: string|callable(\PhpAmqpLib\Message\AMQPMessage): (int|null),
 *   idleTimeout?: int,
 *   consumerTag?: string,
 *   qos?: QosOptions,
 *   auto_setup_fabric_enabled?: bool,
 *   signals_enabled?: bool,
 * }
 */
class Consumer extends AbstractOptions
{
    protected string $connection = 'default';

    protected ?Exchange $exchange = null;

    protected ?Queue $queue = null;

    /** @var null|string|callable(\PhpAmqpLib\Message\AMQPMessage): (int|null) */
    protected $callback;

    protected int $idleTimeout = 0;

    protected ?string $consumerTag = null;

    protected ?Qos $qos = null;

    protected bool $autoSetupFabricEnabled = true;

    protected bool $signalsEnabled = true;

    protected string $description = '';

    /**
     * @psalm-param ConsumerOptions $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * @return Exchange
     */
    public function getExchange(): ?Exchange
    {
        return $this->exchange;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     *
     * @param array<string, mixed>|Exchange $exchange
     *
     * @throws InvalidArgumentException
     */
    public function setExchange($exchange): void
    {
        if (is_array($exchange)) {
            $exchange = new Exchange($exchange);
        }
        if (! $exchange instanceof Exchange) {
            throw new InvalidArgumentException(
                'Parameter "exchange" should be array or an instance of Exchange options'
            );
        }
        $this->exchange = $exchange;
    }

    public function getQueue(): Queue
    {
        if (! $this->queue) {
            throw new \RuntimeException('No queue configuration for consumer');
        }

        return $this->queue;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     *
     * @param array<string, mixed>|Queue $queue
     *
     * @throws InvalidArgumentException
     */
    public function setQueue($queue): void
    {
        if (is_array($queue)) {
            $queue = new Queue($queue);
        }
        if (! $queue instanceof Queue) {
            throw new InvalidArgumentException('Parameter "queue" should be array or an instance of Queue options');
        }
        $this->queue = $queue;
    }

    /**
     * @return null|string|(callable(\PhpAmqpLib\Message\AMQPMessage): (int|null))
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     *
     * @param null|string|callable(\PhpAmqpLib\Message\AMQPMessage): (int|null) $callback
     */
    public function setCallback($callback): void
    {
        $this->callback = $callback;
    }

    public function getIdleTimeout(): int
    {
        return $this->idleTimeout;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setIdleTimeout(int $idleTimeout): void
    {
        $this->idleTimeout = $idleTimeout;
    }

    public function getConsumerTag(): ?string
    {
        return $this->consumerTag;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setConsumerTag(string $consumerTag): void
    {
        $this->consumerTag = $consumerTag;
    }

    public function getQos(): ?Qos
    {
        return $this->qos;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     *
     * @param Qos|array<string, mixed> $qos
     *
     * @throws InvalidArgumentException
     */
    public function setQos($qos): void
    {
        if (is_array($qos)) {
            $qos = new Qos($qos);
        }
        if (! $qos instanceof Qos) {
            throw new InvalidArgumentException('Parameter "qos" should be array or an instance of Qos options');
        }
        $this->qos = $qos;
    }

    public function isAutoSetupFabricEnabled(): bool
    {
        return $this->autoSetupFabricEnabled;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setAutoSetupFabricEnabled(bool $autoSetupFabricEnabled): void
    {
        $this->autoSetupFabricEnabled = $autoSetupFabricEnabled;
    }

    public function isSignalsEnabled(): bool
    {
        return $this->signalsEnabled;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setSignalsEnabled(bool $signalsEnabled): void
    {
        $this->signalsEnabled = $signalsEnabled;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
