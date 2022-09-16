<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

/**
 * @psalm-type QueueOptions = array{
 *   name: string,
 *   passive?: bool,
 *   durable?: bool,
 *   auto_delete?: bool,
 *   exclusive?: bool,
 *   arguments?: array<string, mixed>,
 *   ticket?: int,
 *   routingKeys?: list<string>
 * }
 */
class Queue extends AbstractOptions
{
    protected string $name = '';

    protected bool $passive = false;

    protected bool $durable = true;

    protected bool $autoDelete = false;

    protected bool $exclusive = false;

    /** @var array<string, mixed> */
    protected array $arguments = [];

    protected int $ticket = 0;

    /** @var string[] */
    protected array $routingKeys = [];

    /**
     * @psalm-param QueueOptions $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
