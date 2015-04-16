<?php

namespace RabbitMqModule\Options\Connection;

class SSLConnection extends StreamConnection
{
    /**
     * @var array
     */
    protected $sslOptions;

    /**
     * @return array
     */
    public function getSslOptions()
    {
        return $this->sslOptions;
    }

    /**
     * @param array $sslOptions
     * @return $this
     */
    public function setSslOptions(array $sslOptions)
    {
        $this->sslOptions = $sslOptions;
        return $this;
    }
}
