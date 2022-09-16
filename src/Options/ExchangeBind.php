<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use InvalidArgumentException;
use function is_array;

/**
 * @psalm-type ExchangeBindOptions = array{
 *   exchange: array{name: string}|Exchange,
 *   routingKeys?: non-empty-list<string>
 * }
 */
class ExchangeBind extends AbstractOptions
{
    protected ?Exchange $exchange = null;

    /**
     * @psalm-param list<string>
     * @var string[]
     */
    protected array $routingKeys = [];

    /**
     * @psalm-param ExchangeBindOptions $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getExchange(): Exchange
    {
        if (! $this->exchange) {
            throw new \RuntimeException('No exchange configuration for exchange bind');
        }

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
