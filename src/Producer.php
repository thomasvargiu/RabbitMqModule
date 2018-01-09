<?php

declare(strict_types=1);

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;

class Producer extends BaseAmqp implements ProducerInterface
{
    /**
     * @var string
     */
    protected $contentType = 'text/plain';
    /**
     * @var int
     */
    protected $deliveryMode = 2;
    /**
     * @var bool
     */
    protected $reconnectEnabled = false;

    /**
     * @return bool
     */
    public function isReconnectEnabled(): bool
    {
        return $this->reconnectEnabled;
    }

    /**
     * @param bool $reconnectEnabled
     */
    public function setReconnectEnabled(bool $reconnectEnabled): void
    {
        $this->reconnectEnabled = $reconnectEnabled;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * @return int
     */
    public function getDeliveryMode(): int
    {
        return $this->deliveryMode;
    }

    /**
     * @param int $deliveryMode
     */
    public function setDeliveryMode(int $deliveryMode): void
    {
        $this->deliveryMode = $deliveryMode;
    }

    /**
     * @param string $body
     * @param string $routingKey
     * @param array  $properties
     */
    public function publish(string $body, string $routingKey = '', array $properties = []): void
    {
        $properties = array_merge(
            ['content_type' => $this->getContentType(), 'delivery_mode' => $this->getDeliveryMode()],
            $properties
        );
        $message = new AMQPMessage($body, $properties);

        if ($this->reconnectEnabled && false === $this->getConnection()->select(1)) {
            $this->reconnect();
        }

        if ($this->isAutoSetupFabricEnabled()) {
            $this->setupFabric();
        }

        $this->getChannel()->basic_publish(
            $message,
            $this->getExchangeOptions()->getName(),
            $routingKey
        );
    }
}
