<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use InvalidArgumentException;
use Laminas\Stdlib\AbstractOptions;

class Exchange extends AbstractOptions
{
    /** @var null|string */
    protected $name;

    /** @var null|string */
    protected $type;

    /** @var bool */
    protected $passive = false;

    /** @var bool */
    protected $durable = true;

    /** @var bool */
    protected $autoDelete = false;

    /** @var bool */
    protected $internal = false;

    /** @var bool */
    protected $noWait = false;

    /** @var bool */
    protected $declare = true;

    /** @var array<string, mixed> */
    protected $arguments = [];

    /** @var int */
    protected $ticket = 0;

    /** @var ExchangeBind[] */
    protected $exchangeBinds = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function isPassive(): bool
    {
        return $this->passive;
    }

    public function setPassive(bool $passive): void
    {
        $this->passive = $passive;
    }

    public function isDurable(): bool
    {
        return $this->durable;
    }

    public function setDurable(bool $durable): void
    {
        $this->durable = $durable;
    }

    public function isAutoDelete(): bool
    {
        return $this->autoDelete;
    }

    public function setAutoDelete(bool $autoDelete): void
    {
        $this->autoDelete = $autoDelete;
    }

    public function isInternal(): bool
    {
        return $this->internal;
    }

    /**
     * @param bool $internal
     */
    public function setInternal($internal): void
    {
        $this->internal = $internal;
    }

    public function isNoWait(): bool
    {
        return $this->noWait;
    }

    public function setNoWait(bool $noWait): void
    {
        $this->noWait = $noWait;
    }

    public function isDeclare(): bool
    {
        return $this->declare;
    }

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
     * @param array<string, mixed>|ExchangeBind[] $exchangeBinds
     */
    public function setExchangeBinds(array $exchangeBinds): void
    {
        $this->exchangeBinds = [];
        foreach ($exchangeBinds as $bind) {
            $this->addExchangeBind($bind);
        }
    }

    /**
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
