<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use InvalidArgumentException;

/**
 * @psalm-import-type ExchangeBindOptions from ExchangeBind
 *
 * @psalm-type ExchangeOptions = array{
 *   name: string,
 *   type?: 'direct' | 'fanout' | 'topic' | string,
 *   passive?: bool,
 *   durable?: bool,
 *   auto_delete?: bool,
 *   internal?: bool,
 *   declare?: bool,
 *   arguments?: array<string, mixed>,
 *   ticket?: int,
 *   exchangeBinds?: list<ExchangeBindOptions>
 * }
 */
class Exchange extends AbstractOptions
{
    protected string $name = '';

    protected string $type = 'direct';

    protected bool $passive = false;

    protected bool $durable = true;

    protected bool $autoDelete = false;

    protected bool $internal = false;

    protected bool $declare = true;

    /** @var array<string, mixed> */
    protected array $arguments = [];

    protected int $ticket = 0;

    /** @var ExchangeBind[] */
    protected array $exchangeBinds = [];

    /**
     * @psalm-param ExchangeOptions $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function isPassive(): bool
    {
        return $this->passive;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setPassive(bool $passive): void
    {
        $this->passive = $passive;
    }

    public function isDurable(): bool
    {
        return $this->durable;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setDurable(bool $durable): void
    {
        $this->durable = $durable;
    }

    public function isAutoDelete(): bool
    {
        return $this->autoDelete;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setAutoDelete(bool $autoDelete): void
    {
        $this->autoDelete = $autoDelete;
    }

    public function isInternal(): bool
    {
        return $this->internal;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setInternal(bool $internal): void
    {
        $this->internal = $internal;
    }

    public function isDeclare(): bool
    {
        return $this->declare;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setDeclare(bool $declare): void
    {
        $this->declare = $declare;
    }

    /**
     * @return array<string, mixed>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     *
     * @param array<string, mixed> $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    public function getTicket(): int
    {
        return $this->ticket;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setTicket(int $ticket): void
    {
        $this->ticket = $ticket;
    }

    /**
     * @return ExchangeBind[]
     */
    public function getExchangeBinds(): array
    {
        return $this->exchangeBinds;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     *
     * @param array<array<string, mixed>>|ExchangeBind[] $exchangeBinds
     */
    public function setExchangeBinds(array $exchangeBinds): void
    {
        $this->exchangeBinds = [];
        foreach ($exchangeBinds as $bind) {
            $this->addExchangeBind($bind);
        }
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     *
     * @param array<string, mixed>|ExchangeBind $bind
     *
     * @throws InvalidArgumentException
     */
    public function addExchangeBind($bind): void
    {
        if (is_array($bind)) {
            $bind = new ExchangeBind($bind);
        }
        if (! $bind instanceof ExchangeBind) {
            throw new InvalidArgumentException('Invalid exchange bind options');
        }
        $this->exchangeBinds[] = $bind;
    }
}
