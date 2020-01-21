<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use Laminas\Stdlib\AbstractOptions;

class Queue extends AbstractOptions
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
    protected $exclusive = false;

    /** @var bool */
    protected $noWait = false;

    /** @var array<string, mixed> */
    protected $arguments = [];

    /** @var int */
    protected $ticket = 0;

    /** @var string[] */
    protected $routingKeys = [];

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

    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    public function setExclusive(bool $exclusive): void
    {
        $this->exclusive = $exclusive;
    }

    public function isNoWait(): bool
    {
        return $this->noWait;
    }

    public function setNoWait(bool $noWait): void
    {
        $this->noWait = $noWait;
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
