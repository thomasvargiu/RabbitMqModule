<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;
use RabbitMqModule\Options\ExchangeOptionsAwareInterface;
use RabbitMqModule\Options\Producer as ProducerOptions;
use RabbitMqModule\Options\QueueOptionsAwareInterface;
use RabbitMqModule\Service\RabbitMqServiceAwareInterface;
use RabbitMqModule\Service\RabbitMqServiceAwareTrait;
use RabbitMqModule\Service\SetupFabricAwareInterface;
use Traversable;

class Producer extends BaseAmqp implements
    SetupFabricAwareInterface,
    RabbitMqServiceAwareInterface
{

    use RabbitMqServiceAwareTrait;

    /**
     * @var string
     */
    protected $contentType = 'text/plain';
    /**
     * @var int
     */
    protected $deliveryMode = 2;
    /**
     * @var ProducerOptions
     */
    protected $options;

    /**
     * @return Options\Queue
     */
    public function getQueueOptions()
    {
        return $this->getOptions()->getQueue();
    }

    /**
     * @return Options\Exchange
     */
    public function getExchangeOptions()
    {
        return $this->getOptions()->getExchange();
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return int
     */
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }

    /**
     * @param int $deliveryMode
     * @return $this
     */
    public function setDeliveryMode($deliveryMode)
    {
        $this->deliveryMode = $deliveryMode;
        return $this;
    }

    /**
     * @return ProducerOptions
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->options = new ProducerOptions();
        }
        return $this->options;
    }

    /**
     * @param ProducerOptions $options
     * @return $this
     */
    public function setOptions(ProducerOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param string $body
     * @param string $routingKey
     * @param array $properties
     *
     * @return $this
     */
    public function publish($body, $routingKey = '', array $properties = [])
    {
        if ($this->getOptions()->isAutoSetupFabricEnabled()) {
            $this->setupFabric();
        }
        $properties = array_merge(
            ['content_type' => $this->getContentType(), 'delivery_mode' => $this->getDeliveryMode()],
            $properties
        );
        $message = new AMQPMessage((string)$body, $properties);
        $this->getChannel()->basic_publish(
            $message,
            $this->getOptions()->getExchange()->getName(),
            (string)$routingKey
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function setupFabric()
    {
        if (!$this->exchangeDeclared) {
            $exchangeOptions = $this->getOptions()->getExchange();
            $this->getRabbitMqService()->declareExchange($this->getChannel(), $exchangeOptions);
            $this->exchangeDeclared = true;
        }

        $queueOptions = $this->getOptions()->getQueue();

        if (!$this->queueDeclared && $queueOptions) {
            $exchangeOptions = $this->getOptions()->getExchange();
            $this->getRabbitMqService()->declareQueue($this->getChannel(), $exchangeOptions, $queueOptions);
            $this->queueDeclared = true;
        }

        return $this;
    }
}
