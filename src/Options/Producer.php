<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use InvalidArgumentException;

/**
 * @psalm-import-type ExchangeOptions from Exchange
 * @psalm-import-type QueueOptions from Queue
 * @psalm-type ProducerOptions = array{
 *   connection?: string,
 *   exchange: ExchangeOptions|Exchange,
 *   queue?: QueueOptions|Queue,
 *   auto_setup_fabric_enabled?: bool,
 * }
 */
class Producer extends AbstractOptions
{
    protected string $connection = 'default';

    protected ?Exchange $exchange = null;

    protected ?Queue $queue = null;

    protected bool $autoSetupFabricEnabled = true;

    /**
     * @psalm-param ProducerOptions $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    public function getExchange(): Exchange
    {
        if (! $this->exchange) {
            throw new \RuntimeException('No exchange configuration for producer');
        }

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

    public function isAutoSetupFabricEnabled(): bool
    {
        return $this->autoSetupFabricEnabled;
    }

    public function setAutoSetupFabricEnabled(bool $autoSetupFabricEnabled): void
    {
        $this->autoSetupFabricEnabled = $autoSetupFabricEnabled;
    }
}
