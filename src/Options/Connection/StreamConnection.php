<?php

namespace RabbitMqModule\Options\Connection;

class StreamConnection extends AbstractConnection
{
    /**
     * @var int
     */
    protected $connectionTimeout = 3;
    /**
     * @var int
     */
    protected $heartbeat = 0;

    /**
     * @return int
     */
    public function getConnectionTimeout()
    {
        return $this->connectionTimeout;
    }

    /**
     * @param int $connectionTimeout
     * @return $this
     */
    public function setConnectionTimeout($connectionTimeout)
    {
        $this->connectionTimeout = $connectionTimeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeartbeat()
    {
        return $this->heartbeat;
    }

    /**
     * @param int $heartbeat
     * @return $this
     */
    public function setHeartbeat($heartbeat)
    {
        $this->heartbeat = $heartbeat;
        return $this;
    }
}
