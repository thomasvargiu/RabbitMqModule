<?php

namespace RabbitMqModule\Options;

use Zend\Stdlib\AbstractOptions;

class Qos extends AbstractOptions
{
    /**
     * @var int
     */
    protected $prefetchSize = 0;
    /**
     * @var int
     */
    protected $prefetchCount = 0;
    /**
     * @var bool
     */
    protected $global = false;

    /**
     * @return int
     */
    public function getPrefetchSize()
    {
        return $this->prefetchSize;
    }

    /**
     * @param int $prefetchSize
     * @return $this
     */
    public function setPrefetchSize($prefetchSize)
    {
        $this->prefetchSize = $prefetchSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrefetchCount()
    {
        return $this->prefetchCount;
    }

    /**
     * @param int $prefetchCount
     * @return $this
     */
    public function setPrefetchCount($prefetchCount)
    {
        $this->prefetchCount = $prefetchCount;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isGlobal()
    {
        return $this->global;
    }

    /**
     * @param boolean $global
     * @return $this
     */
    public function setGlobal($global)
    {
        $this->global = $global;
        return $this;
    }
}
