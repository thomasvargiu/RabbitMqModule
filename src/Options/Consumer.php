<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use InvalidArgumentException;
use Laminas\Stdlib\AbstractOptions;

class Consumer extends AbstractOptions
{
    /**
     * @var string
     */
    protected $connection = 'default';

    /**
     * @var null|Exchange
     */
    protected $exchange;

    /**
     * @var null|Queue
     */
    protected $queue;

    /**
     * @var null|string|callable
     */
    protected $callback;

    /**
     * @var int
     */
    protected $idleTimeout = 0;

    /**
     * @var null|string
     */
    protected $consumerTag;

    /**
     * @var null|Qos
     */
    protected $qos;

    /**
     * @var bool
     */
    protected $autoSetupFabricEnabled = true;

    /**
     * @var bool
     */
    protected $signalsEnabled = true;

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * @param string $connection
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
     * @param array|Exchange $exchange
     *
     * @throws InvalidArgumentException
     */
    public function setExchange($exchange): void
    {
        if (\is_array($exchange)) {
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
     * @param array|Queue $queue
     *
     * @throws \InvalidArgumentException
     */
    public function setQueue($queue): void
    {
        if (\is_array($queue)) {
            $queue = new Queue($queue);
        }
        if (! $queue instanceof Queue) {
            throw new InvalidArgumentException('Parameter "queue" should be array or an instance of Queue options');
        }
        $this->queue = $queue;
    }

    /**
     * @return string|callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param string|callable $callback
     */
    public function setCallback($callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @return int
     */
    public function getIdleTimeout(): int
    {
        return $this->idleTimeout;
    }

    /**
     * @param int $idleTimeout
     */
    public function setIdleTimeout(int $idleTimeout): void
    {
        $this->idleTimeout = $idleTimeout;
    }

    /**
     * @return null|string
     */
    public function getConsumerTag(): ?string
    {
        return $this->consumerTag;
    }

    /**
     * @param string $consumerTag
     */
    public function setConsumerTag(string $consumerTag): void
    {
        $this->consumerTag = $consumerTag;
    }

    /**
     * @return null|Qos
     */
    public function getQos(): ?Qos
    {
        return $this->qos;
    }

    /**
     * @param array|Qos $qos
     *
     * @throws InvalidArgumentException
     */
    public function setQos($qos): void
    {
        if (\is_array($qos)) {
            $qos = new Qos($qos);
        }
        if (! $qos instanceof Qos) {
            throw new InvalidArgumentException('Parameter "qos" should be array or an instance of Qos options');
        }
        $this->qos = $qos;
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
     * @return bool
     */
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

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
