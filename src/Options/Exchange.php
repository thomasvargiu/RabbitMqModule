<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use Laminas\Stdlib\AbstractOptions;

class Exchange extends AbstractOptions
{
    /**
     * @var null|string
     */
    protected $name;
    /**
     * @var null|string
     */
    protected $type;
    /**
     * @var bool
     */
    protected $passive = false;
    /**
     * @var bool
     */
    protected $durable = true;
    /**
     * @var bool
     */
    protected $autoDelete = false;
    /**
     * @var bool
     */
    protected $internal = false;
    /**
     * @var bool
     */
    protected $noWait = false;
    /**
     * @var bool
     */
    protected $declare = true;
    /**
     * @var array
     */
    protected $arguments = [];
    /**
     * @var int
     */
    protected $ticket = 0;
    /**
     * @var ExchangeBind[]
     */
    protected $exchangeBinds = [];

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isPassive(): bool
    {
        return $this->passive;
    }

    /**
     * @param bool $passive
     */
    public function setPassive(bool $passive): void
    {
        $this->passive = $passive;
    }

    /**
     * @return bool
     */
    public function isDurable(): bool
    {
        return $this->durable;
    }

    /**
     * @param bool $durable
     */
    public function setDurable(bool $durable): void
    {
        $this->durable = $durable;
    }

    /**
     * @return bool
     */
    public function isAutoDelete(): bool
    {
        return $this->autoDelete;
    }

    /**
     * @param bool $autoDelete
     */
    public function setAutoDelete(bool $autoDelete): void
    {
        $this->autoDelete = $autoDelete;
    }

    /**
     * @return bool
     */
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

    /**
     * @return bool
     */
    public function isNoWait(): bool
    {
        return $this->noWait;
    }

    /**
     * @param bool $noWait
     */
    public function setNoWait(bool $noWait): void
    {
        $this->noWait = $noWait;
    }

    /**
     * @return bool
     */
    public function isDeclare(): bool
    {
        return $this->declare;
    }

    /**
     * @param bool $declare
     */
    public function setDeclare(bool $declare): void
    {
        $this->declare = $declare;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @return int
     */
    public function getTicket(): int
    {
        return $this->ticket;
    }

    /**
     * @param int $ticket
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
     * @param array|ExchangeBind[] $exchangeBinds
     */
    public function setExchangeBinds(array $exchangeBinds): void
    {
        $this->exchangeBinds = [];
        foreach ($exchangeBinds as $bind) {
            $this->addExchangeBind($bind);
        }
    }

    /**
     * @param array|ExchangeBind $bind
     * @throws \InvalidArgumentException
     */
    public function addExchangeBind($bind): void
    {
        if (is_array($bind)) {
            $bind = new ExchangeBind($bind);
        }
        if (!$bind instanceof ExchangeBind) {
            throw new \InvalidArgumentException('Invalid exchange bind options');
        }
        $this->exchangeBinds[] = $bind;
    }
}
