<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

/**
 * @psalm-type SerializerOptions = array{name: string, options?: array<string, mixed>}
 * @psalm-type RpcClientOptions = array{
 *   connection?: string,
 *   serializer?: string|SerializerOptions,
 * }
 */
class RpcClient extends AbstractOptions
{
    protected string $connection = 'default';

    /**
     * @psalm-var null|string|SerializerOptions
     * @var string|array|null
     */
    protected $serializer;

    /**
     * @psalm-param RpcClientOptions $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * @psalm-return null|string|SerializerOptions
     * @return array|string|null
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param null|string|SerializerOptions $serializer
     */
    public function setSerializer($serializer = null): void
    {
        $this->serializer = $serializer;
    }
}
