<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use InvalidArgumentException;
use function is_array;
use Laminas\Stdlib\AbstractOptions;
use RabbitMqModule\ConsumerInterface;

class Consumer extends AbstractOptions
{
    /** @var string */
    protected $connection = 'default';

    /** @var null|Exchange */
    protected $exchange;

    /** @var null|Queue */
    protected $queue;

    /** @var null|string|ConsumerInterface|callable(\PhpAmqpLib\Message\AMQPMessage): int|null */
    protected $callback;

    /** @var int */
    protected $idleTimeout = 0;

    /** @var null|string */
    protected $consumerTag;

    /** @var null|Qos */
    protected $qos;

    /** @var bool */
    protected $autoSetupFabricEnabled = true;

    /** @var bool */
    protected $signalsEnabled = true;

    /** @var string */
    protected $description = '';

    public function getConnection(): string
    {
        return $this->connection;
    }

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

    /**
     * @return Queue
     */
    public function getQueue(): ?Queue
    {
        return $this->queue;
    }

    /**
     * @param array<string, mixed>|Queue $queue
     *
     * @throws \InvalidArgumentException
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
     * @return null|string|ConsumerInterface|callable(\PhpAmqpLib\Message\AMQPMessage): int|null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param null|string|ConsumerInterface|callable(\PhpAmqpLib\Message\AMQPMessage): int|null $callback
     */
    public function setCallback($callback): void
    {
        $this->callback = $callback;
    }

    public function getIdleTimeout(): int
    {
        return $this->idleTimeout;
    }

    public function setIdleTimeout(int $idleTimeout): void
    {
        $this->idleTimeout = $idleTimeout;
    }

    public function getConsumerTag(): ?string
    {
        return $this->consumerTag;
    }

    public function setConsumerTag(string $consumerTag): void
    {
        $this->consumerTag = $consumerTag;
    }

    public function getQos(): ?Qos
    {
        return $this->qos;
    }

    /**
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

    public function setAutoSetupFabricEnabled(bool $autoSetupFabricEnabled): void
    {
        $this->autoSetupFabricEnabled = $autoSetupFabricEnabled;
    }

    public function isSignalsEnabled(): bool
    {
        return $this->signalsEnabled;
    }

    /**
     * @param bool $signalsEnabled
     */
    public function setSignalsEnabled($signalsEnabled): void
    {
        $this->signalsEnabled = $signalsEnabled;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
