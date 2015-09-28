<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

abstract class BaseConsumer extends BaseAmqp implements
    EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @var string
     */
    protected $consumerTag;
    /**
     * @var callable
     */
    protected $callback;
    /**
     * @var bool
     */
    protected $forceStop = false;
    /**
     * @var int
     */
    protected $idleTimeout = 0;
    /**
     * @var bool
     */
    protected $signalsEnabled = true;

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
    public function setSignalsEnabled($signalsEnabled = true)
    {
        $this->signalsEnabled = $signalsEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getConsumerTag()
    {
        if (!$this->consumerTag) {
            $this->consumerTag = sprintf('PHPPROCESS_%s_%s', gethostname(), getmypid());
        }

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
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Invalid callback provided');
        }
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
     * Start consumer.
     */
    public function start()
    {
        $this->setupConsumer();
        while (count($this->getChannel()->callbacks)) {
            $this->getChannel()->wait();
        }
    }

    protected function setupConsumer()
    {
        if ($this->isAutoSetupFabricEnabled()) {
            $this->setupFabric();
        }

        $this->getChannel()->basic_consume(
            $this->getQueueOptions()->getName(),
            $this->getConsumerTag(),
            false,
            false,
            false,
            false,
            function ($message) {
                $this->processMessage($message);
            }
        );
    }

    /**
     * Sets the qos settings for the current channel
     * Consider that prefetchSize and global do not work with rabbitMQ version <= 8.0.
     *
     * @param int  $prefetchSize
     * @param int  $prefetchCount
     * @param bool $global
     *
     * @return $this
     */
    public function setQosOptions($prefetchSize = 0, $prefetchCount = 0, $global = false)
    {
        $this->getChannel()->basic_qos($prefetchSize, $prefetchCount, $global);

        return $this;
    }

    protected function maybeStopConsumer()
    {
        // @codeCoverageIgnoreStart
        if (extension_loaded('pcntl') && $this->isSignalsEnabled()) {
            if (!function_exists('pcntl_signal_dispatch')) {
                throw new \BadFunctionCallException(
                    'Function \'pcntl_signal_dispatch\' is referenced in the php.ini' .
                    '\'disable_functions\' and can\'t be called.'
                );
            }
            pcntl_signal_dispatch();
        }
        // @codeCoverageIgnoreEnd

        if ($this->forceStop) {
            $this->stopConsuming();
        }

        return $this;
    }

    public function forceStopConsumer()
    {
        $this->forceStop = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function stopConsuming()
    {
        $this->getChannel()->basic_cancel($this->getConsumerTag());

        return $this;
    }

    /**
     * @param AMQPMessage $message
     */
    abstract public function processMessage(AMQPMessage $message);
}
