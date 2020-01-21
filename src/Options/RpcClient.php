<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use function array_key_exists;
use InvalidArgumentException;
use function is_array;
use function is_string;
use Laminas\Serializer\Adapter\AdapterInterface as SerializerInterface;
use Laminas\Serializer\Serializer;
use Laminas\Stdlib\AbstractOptions;

class RpcClient extends AbstractOptions
{
    /**
     * @var string
     */
    protected $connection = 'default';

    /**
     * @var SerializerInterface|null
     */
    protected $serializer;

    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * @param string $connection
     */
    public function setConnection($connection): void
    {
        $this->connection = $connection;
    }

    public function getSerializer(): ?SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @param null|SerializerInterface|string|array{name: string, options: null|array<string,mixed>} $serializer
     *
     * @throws InvalidArgumentException
     */
    public function setSerializer($serializer = null): void
    {
        if (is_array($serializer)) {
            if (! array_key_exists('name', $serializer)) {
                throw new InvalidArgumentException('A serializer name should be provided');
            }
            $name = $serializer['name'];
            $serializer = Serializer::factory($name, $serializer['options'] ?? null);
        } elseif (is_string($serializer)) {
            $serializer = Serializer::factory($serializer);
        }

        $this->serializer = $serializer;
    }
}
