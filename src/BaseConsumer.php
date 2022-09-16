<?php

declare(strict_types=1);

namespace RabbitMqModule;

use BadFunctionCallException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use RabbitMqModule\Options\Queue;
use function extension_loaded;
use function function_exists;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * @psalm-type ConsumerHandler = callable(AMQPMessage): (int|null|void)
 */
abstract class BaseConsumer extends BaseAmqp
{
    protected ?string $consumerTag = null;

    /**
     * @psalm-var callable(AMQPMessage): (int|void)
     * @var callable
     */
    protected $callback;

    protected bool $forceStop = false;

    protected int $idleTimeout = 0;

    protected bool $signalsEnabled = true;

    protected string $queueName;

    /**
     * @psalm-param callable(AMQPMessage): (int|void) $callback
     */
    public function __construct(AbstractConnection $connection, Queue $queueOptions, callable $callback, AMQPChannel $channel = null)
    {
        parent::__construct($connection, $channel);
        $this->setQueueOptions($queueOptions);
        $this->queueName = $queueOptions->getName();
        $this->callback = $callback;
    }

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

    /**
     * @psalm-return callable(AMQPMessage): (int|void)
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * @param callable(AMQPMessage): (int|void) $callback
     * @return void
     */
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

        while ($this->getChannel()->is_consuming()) {
            $this->getChannel()->wait();
        }
    }

    protected function setupConsumer(): void
    {
        if ($this->isAutoSetupFabricEnabled()) {
            $this->setupFabric();
        }

        $this->getChannel()->basic_consume(
            $this->queueName,
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
     */
    public function setQosOptions(int $prefetchSize = 0, int $prefetchCount = 0, bool $global = false): void
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
