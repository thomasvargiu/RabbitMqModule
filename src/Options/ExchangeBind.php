<?php

namespace RabbitMqModule\Options;

use Zend\Stdlib\AbstractOptions;

class ExchangeBind extends AbstractOptions
{
    /**
     * @var Exchange
     */
    protected $exchange;
    /**
     * @var array
     */
    protected $routingKeys = [];

    /**
     * @return Exchange
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @param array|Exchange $exchange
     *
     * @return $this
     */
    public function setExchange($exchange)
    {
        if (is_array($exchange)) {
            $exchange = new Exchange($exchange);
        }
        if (!$exchange instanceof Exchange) {
            throw new \InvalidArgumentException('Parameter "exchange" should be array or an instance of Exchange options');
        }
        $this->exchange = $exchange;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoutingKeys()
    {
        return $this->routingKeys;
    }

    /**
     * @param array $routingKeys
     * @return $this
     */
    public function setRoutingKeys($routingKeys)
    {
        $this->routingKeys = $routingKeys;
        return $this;
    }
}
