<?php

namespace RabbitMqModule\Options;

use Zend\Serializer\Serializer;
use Zend\Stdlib\AbstractOptions;
use Zend\Serializer\Adapter\AdapterInterface as SerializerInterface;

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
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $connection
     *
     * @return $this
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
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
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setSerializer($serializer = null)
    {
        if (is_array($serializer)) {
            if (!array_key_exists('name', $serializer)) {
                throw new \InvalidArgumentException('A serializer name should be provided');
            }
            $name = $serializer['name'];
            $options = array_key_exists('options', $serializer) ? $serializer['options'] : null;
            $serializer = Serializer::factory($name, $options);
        } elseif (is_string($serializer)) {
            $serializer = Serializer::factory($serializer);
        }
        if (null !== $serializer && !$serializer instanceof SerializerInterface) {
            throw new \InvalidArgumentException('Invalid serializer instance or options');
        }
        $this->serializer = $serializer;
        return $this;
    }
}
