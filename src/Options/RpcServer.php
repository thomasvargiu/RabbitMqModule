<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use function array_key_exists;
use InvalidArgumentException;
use function is_array;
use function is_string;
use Laminas\Serializer\Adapter\AdapterInterface as SerializerInterface;
use Laminas\Serializer\Serializer;

class RpcServer extends Consumer
{
    /**
     * @var SerializerInterface|null
     */
    protected $serializer;

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
            $options = $serializer['options'] ?? null;
            $serializer = Serializer::factory($name, $options);
        } elseif (is_string($serializer)) {
            $serializer = Serializer::factory($serializer);
        }

        $this->serializer = $serializer;
    }
}
