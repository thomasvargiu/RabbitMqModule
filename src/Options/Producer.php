<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use Zend\Stdlib\AbstractOptions;

class Producer extends AbstractOptions
{
    /**
     * @var string
     */
    protected $connection = 'default';
    /**
     * @var Exchange
     */
    protected $exchange;
    /**
     * @var Queue
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
     * @return null|Exchange
     */
    public function getExchange(): ?Exchange
    {
        return $this->exchange;
    }

    /**
     * @param array|Exchange $exchange
     *
     * @throws \InvalidArgumentException
     */
    public function setExchange($exchange): void
    {
        if (is_array($exchange)) {
            $exchange = new Exchange($exchange);
        }
        if (!$exchange instanceof Exchange) {
            throw new \InvalidArgumentException(
                'Parameter "exchange" should be array or an instance of Exchange options'
            );
        }
        $this->exchange = $exchange;
    }

    /**
     * @return null|Queue
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
        if (!$queue instanceof Queue) {
            throw new \InvalidArgumentException(
                'Parameter "queue" should be array or an instance of Queue options'
            );
        }
        $this->queue = $queue;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
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
}
