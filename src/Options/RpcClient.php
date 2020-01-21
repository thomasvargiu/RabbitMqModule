<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use Laminas\Serializer\Serializer;
use Laminas\Stdlib\AbstractOptions;
use Laminas\Serializer\Adapter\AdapterInterface as SerializerInterface;

class RpcClient extends AbstractOptions
{
    /**
     * @var string
     */
    protected $connection = 'default';
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @return string
     */
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

    /**
     * @return mixed
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param null|string|array|SerializerInterface $serializer
     *
     * @throws \InvalidArgumentException
     */
    public function setSerializer($serializer = null): void
    {
        if (\is_array($serializer)) {
            if (! \array_key_exists('name', $serializer)) {
                throw new \InvalidArgumentException('A serializer name should be provided');
            }
            $name = $serializer['name'];
            $options = \array_key_exists('options', $serializer) ? $serializer['options'] : null;
            $serializer = Serializer::factory($name, $options);
        } elseif (\is_string($serializer)) {
            $serializer = Serializer::factory($serializer);
        }
        if (null !== $serializer && !$serializer instanceof SerializerInterface) {
            throw new \InvalidArgumentException('Invalid serializer instance or options');
        }
        $this->serializer = $serializer;
    }
}
