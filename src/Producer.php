<?php

namespace RabbitMqModule;

use PhpAmqpLib\Message\AMQPMessage;
use RabbitMqModule\Service\SetupFabricAwareInterface;

class Producer extends BaseAmqp implements
    SetupFabricAwareInterface
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
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
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
     *
     * @return $this
     */
    public function setDeliveryMode($deliveryMode)
    {
        $this->deliveryMode = $deliveryMode;

        return $this;
    }

    /**
     * @param string $body
     * @param string $routingKey
     * @param array  $properties
     *
     * @return $this
     */
    public function publish($body, $routingKey = '', array $properties = [])
    {
        if ($this->isAutoSetupFabricEnabled()) {
            $this->setupFabric();
        }
        $properties = array_merge(
            ['content_type' => $this->getContentType(), 'delivery_mode' => $this->getDeliveryMode()],
            $properties
        );
        $message = new AMQPMessage((string) $body, $properties);
        $this->getChannel()->basic_publish(
            $message,
            $this->getExchangeOptions()->getName(),
            (string) $routingKey
        );

        return $this;
    }
}
