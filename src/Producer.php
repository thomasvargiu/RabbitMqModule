<?php

declare(strict_types=1);

namespace RabbitMqModule;

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use RabbitMqModule\Options\Exchange as ExchangeOptions;

final class Producer extends BaseAmqp implements ProducerInterface
{
    protected string $exchangeName;

    protected string $contentType = 'text/plain';

    protected int $deliveryMode = 2;

    private bool $alreadySetup = false;

    public function __construct(AbstractConnection $connection, ExchangeOptions $exchangeOptions)
    {
        parent::__construct($connection);
        $this->setExchangeOptions($exchangeOptions);
        $this->exchangeName = $exchangeOptions->getName();
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function getDeliveryMode(): int
    {
        return $this->deliveryMode;
    }

    public function setDeliveryMode(int $deliveryMode): void
    {
        $this->deliveryMode = $deliveryMode;
    }

    /**
     * @param array<string, mixed>  $properties
     */
    public function publish(string $body, string $routingKey = '', array $properties = []): void
    {
        if (false === $this->getConnection()->isConnected()) {
            $this->reconnect();
        }

        $properties = array_merge(
            ['content_type' => $this->getContentType(), 'delivery_mode' => $this->getDeliveryMode()],
            $properties
        );
        $message = new AMQPMessage($body, $properties);

        if (false === $this->alreadySetup && $this->isAutoSetupFabricEnabled()) {
            $this->setupFabric();
            $this->alreadySetup = true;
        }

        $this->getChannel()->basic_publish(
            $message,
            $this->exchangeName,
            $routingKey
        );
    }
}
