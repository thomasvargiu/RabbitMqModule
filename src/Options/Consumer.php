<?php

namespace RabbitMqModule\Options;

use Zend\Stdlib\AbstractOptions;

class Consumer extends AbstractOptions
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
     * @var string|callable
     */
    protected $callback;
    /**
     * @var int
     */
    protected $idleTimeout;
    /**
     * @var string
     */
    protected $consumerTag;
    /**
     * @var Qos
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
     */
    public function setExchange($exchange)
    {
        if (is_array($exchange)) {
            $exchange = new Exchange($exchange);
        }
        if (!$exchange instanceof Exchange) {
            throw new \InvalidArgumentException('Parameter "exchange" should be array or an instance of Exchange options');
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
     */
    public function setQueue($queue)
    {
        if (is_array($queue)) {
            $queue = new Queue($queue);
        }
        if (!$queue instanceof Queue) {
            throw new \InvalidArgumentException('Parameter "queue" should be array or an instance of Queue options');
        }
        $this->queue = $queue;

        return $this;
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
     *
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdleTimeout()
    {
        return $this->idleTimeout;
    }

    /**
     * @param int $idleTimeout
     *
     * @return $this
     */
    public function setIdleTimeout($idleTimeout)
    {
        $this->idleTimeout = $idleTimeout;

        return $this;
    }

    /**
     * @return string
     */
    public function getConsumerTag()
    {
        return $this->consumerTag;
    }

    /**
     * @param string $consumerTag
     *
     * @return $this
     */
    public function setConsumerTag($consumerTag)
    {
        $this->consumerTag = $consumerTag;

        return $this;
    }

    /**
     * @return Qos
     */
    public function getQos()
    {
        return $this->qos;
    }

    /**
     * @param array|Qos $qos
     *
     * @return $this
     */
    public function setQos($qos)
    {
        if (is_array($qos)) {
            $qos = new Qos($qos);
        }
        if (!$qos instanceof Qos) {
            throw new \InvalidArgumentException('Parameter "qos" should be array or an instance of Qos options');
        }
        $this->qos = $qos;

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
     * @return bool
     */
    public function isSignalsEnabled()
    {
        return $this->signalsEnabled;
    }

    /**
     * @param bool $signalsEnabled
     *
     * @return $this
     */
    public function setSignalsEnabled($signalsEnabled)
    {
        $this->signalsEnabled = $signalsEnabled;

        return $this;
    }
}
