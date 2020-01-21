<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use InvalidArgumentException;
use Laminas\Stdlib\AbstractOptions;

class Producer extends AbstractOptions
{
    /**
     * @var string
     */
    protected $connection = 'default';

    /**
     * @var Exchange|null
     */
    protected $exchange;

    /**
     * @var Queue|null
     */
    protected $queue;

    /**
     * @var string
     */
    protected $class = \RabbitMqModule\Producer::class;

    /**
     * @var bool
     */
    protected $autoSetupFabricEnabled = true;

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

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

    public function getQueue(): ?Queue
    {
        return $this->queue;
    }

    /**
     * @param array<string, mixed>|Queue $queue
     *
     * @throws InvalidArgumentException
     */
    public function setQueue($queue): void
    {
        if (\is_array($queue)) {
            $queue = new Queue($queue);
        }
        if (! $queue instanceof Queue) {
            throw new InvalidArgumentException(
                'Parameter "queue" should be array or an instance of Queue options'
            );
        }
        $this->queue = $queue;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    public function isAutoSetupFabricEnabled(): bool
    {
        return $this->autoSetupFabricEnabled;
    }

    public function setAutoSetupFabricEnabled(bool $autoSetupFabricEnabled): void
    {
        $this->autoSetupFabricEnabled = $autoSetupFabricEnabled;
    }
}
