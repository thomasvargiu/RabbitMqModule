<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use Laminas\Stdlib\AbstractOptions;

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
    public function getExchange(): Exchange
    {
        return $this->exchange;
    }

    /**
     * @param array|Exchange $exchange
     *
     * @throws \InvalidArgumentException
     */
    public function setExchange($exchange): void
    {
        if (\is_array($exchange)) {
            $exchange = new Exchange($exchange);
        }
        if (! $exchange instanceof Exchange) {
            throw new \InvalidArgumentException(
                'Parameter "exchange" should be array or an instance of Exchange options'
            );
        }
        $this->exchange = $exchange;
    }

    /**
     * @return array
     */
    public function getRoutingKeys(): array
    {
        return $this->routingKeys;
    }

    /**
     * @param array $routingKeys
     */
    public function setRoutingKeys(array $routingKeys): void
    {
        $this->routingKeys = $routingKeys;
    }
}
