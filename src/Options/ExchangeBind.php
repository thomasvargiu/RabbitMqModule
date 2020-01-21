<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use InvalidArgumentException;
use function is_array;
use Laminas\Stdlib\AbstractOptions;

class ExchangeBind extends AbstractOptions
{
    /**
     * @var Exchange
     */
    protected $exchange;

    /**
     * @var string[]
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
     * @param array<string, mixed>|Exchange $exchange
     *
     * @throws InvalidArgumentException
     */
    public function setExchange($exchange): void
    {
        if (is_array($exchange)) {
            $exchange = new Exchange($exchange);
        }
        if (! $exchange instanceof Exchange) {
            throw new InvalidArgumentException(
                'Parameter "exchange" should be array or an instance of Exchange options'
            );
        }
        $this->exchange = $exchange;
    }

    /**
     * @return string[]
     */
    public function getRoutingKeys(): array
    {
        return $this->routingKeys;
    }

    /**
     * @param string[] $routingKeys
     */
    public function setRoutingKeys(array $routingKeys): void
    {
        $this->routingKeys = $routingKeys;
    }
}
