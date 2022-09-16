<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

/**
 * @psalm-type QosOptions = array{prefetch_size?: int, prefetch_count?: int}
 */
class Qos extends AbstractOptions
{
    protected int $prefetchSize = 0;

    protected int $prefetchCount = 0;

    protected bool $global = false;

    /**
     * @psalm-param QosOptions $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getPrefetchSize(): int
    {
        return $this->prefetchSize;
    }

    public function setPrefetchSize(int $prefetchSize): void
    {
        $this->prefetchSize = $prefetchSize;
    }

    public function getPrefetchCount(): int
    {
        return $this->prefetchCount;
    }

    public function setPrefetchCount(int $prefetchCount): void
    {
        $this->prefetchCount = $prefetchCount;
    }

    /**
     * @deprecated
     */
    public function isGlobal(): bool
    {
        return $this->global;
    }

    /**
     * @deprecated
     */
    public function setGlobal(bool $global): void
    {
        $this->global = $global;
    }
}
