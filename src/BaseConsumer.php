<?php

declare(strict_types=1);

namespace RabbitMqModule;

use BadFunctionCallException;
use function count;
use function extension_loaded;
use function function_exists;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use PhpAmqpLib\Message\AMQPMessage;

abstract class BaseConsumer extends BaseAmqp implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /** @var null|string */
    protected $consumerTag;

    /** @var callable */
    protected $callback;

    /** @var bool */
    protected $forceStop = false;

    /** @var int */
    protected $idleTimeout = 0;

    /** @var bool */
    protected $signalsEnabled = true;

    public function isSignalsEnabled(): bool
    {
        return $this->signalsEnabled;
    }

    public function setSignalsEnabled(bool $signalsEnabled = true): void
    {
        $this->signalsEnabled = $signalsEnabled;
    }

    public function getConsumerTag(): string
    {
        if (! $this->consumerTag) {
            $this->consumerTag = sprintf('PHPPROCESS_%s_%s', gethostname(), getmypid());
        }

        return $this->consumerTag;
    }

    public function setConsumerTag(string $consumerTag): void
    {
        $this->consumerTag = $consumerTag;
    }

    public function getCallback(): callable
    {
        return $this->callback;
    }

    public function setCallback(callable $callback): void
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

    /**
     * Start consumer.
     */
    public function start(): void
    {
        $this->setupConsumer();

        while (count($this->getChannel()->callbacks)) {
            $this->getChannel()->wait();
        }
    }

    protected function setupConsumer(): void
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
            [$this, 'processMessage']
        );
    }

    /**
     * Sets the qos settings for the current channel
     * Consider that prefetchSize and global do not work with rabbitMQ version <= 8.0.
     *
     * @param int  $prefetchSize
     * @param int  $prefetchCount
     * @param bool $global
     */
    public function setQosOptions($prefetchSize = 0, $prefetchCount = 0, $global = false): void
    {
        $this->getChannel()->basic_qos($prefetchSize, $prefetchCount, $global);
    }

    protected function maybeStopConsumer(): void
    {
        // @codeCoverageIgnoreStart
        if (extension_loaded('pcntl') && $this->isSignalsEnabled()) {
            if (! function_exists('pcntl_signal_dispatch')) {
                throw new BadFunctionCallException(
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
    }

    public function forceStopConsumer(): void
    {
        $this->forceStop = true;
    }

    public function stopConsuming(): void
    {
        $this->getChannel()->basic_cancel($this->getConsumerTag());
    }

    abstract public function processMessage(AMQPMessage $message): void;
}
