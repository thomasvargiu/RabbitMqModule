<?php

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
    protected $class = 'RabbitMqModule\\Producer';
    /**
     * @var bool
     */
    protected $autoSetupFabricEnabled = true;

    /**
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $connection
     *
     * @return $this
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return Exchange
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @param array|Exchange $exchange
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setExchange($exchange)
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

        return $this;
    }

    /**
     * @return Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param array|Queue $queue
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setQueue($queue)
    {
        if (is_array($queue)) {
            $queue = new Queue($queue);
        }
        if (!$queue instanceof Queue) {
            throw new \InvalidArgumentException(
                'Parameter "queue" should be array or an instance of Queue options'
            );
        }
        $this->queue = $queue;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

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
}
