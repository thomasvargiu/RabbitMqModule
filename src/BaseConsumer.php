<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;
use RabbitMqModule\Options\Consumer as ConsumerOptions;

abstract class BaseConsumer extends BaseAmqp
{
    /**
     * @var ConsumerOptions
     */
    protected $options;

    /**
     * @var string
     */
    protected $consumerTag;

    /**
     * @return ConsumerOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param ConsumerOptions $options
     * @return $this
     */
    public function setOptions(ConsumerOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return string
     */
    public function getConsumerTag()
    {
        if (!$this->consumerTag) {
            $consumerTag = $this->getOptions()->getConsumerTag();
            if (empty($consumerTag)) {
                $consumerTag = sprintf('PHPPROCESS_%s_%s', gethostname(), getmypid());
            }
            $this->consumerTag = $consumerTag;
        }
        return $this->consumerTag;
    }

    /**
     * @param string $consumerTag
     * @return $this
     */
    public function setConsumerTag($consumerTag)
    {
        $this->consumerTag = $consumerTag;
        return $this;
    }

    protected function setupConsumer()
    {
        if ($this->getOptions()->isAutoSetupFabricEnabled()) {
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

    public function processMessage(AMQPMessage $message) {

    }
}
