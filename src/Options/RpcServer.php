<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

/**
 * @psalm-type SerializerOptions = array{name: string, options?: array<string, mixed>}
 *
 * @psalm-import-type ExchangeOptions from Exchange
 * @psalm-import-type QueueOptions from Queue
 * @psalm-import-type QosOptions from Qos
 *
 * @psalm-type RpcServerOptions = array{
 *   connection?: string,
 *   queue: QueueOptions|Queue,
 *   exchange?: ExchangeOptions|Exchange,
 *   callback: string|callable(\PhpAmqpLib\Message\AMQPMessage): void,
 *   idleTimeout?: int,
 *   consumerTag?: string,
 *   qos?: QosOptions,
 *   auto_setup_fabric_enabled?: bool,
 *   signals_enabled?: bool,
 *   serializer?: string|SerializerOptions,
 * }
 */
class RpcServer extends Consumer
{
    /**
     * @psalm-var null|string|SerializerOptions
     *
     * @var string|array|null
     */
    protected $serializer;

    /**
     * @psalm-param RpcServerOptions $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * @psalm-return null|string|SerializerOptions
     *
     * @return array|string|null
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     *
     * @param null|string|SerializerOptions $serializer
     */
    public function setSerializer($serializer = null): void
    {
        $this->serializer = $serializer;
    }
}
