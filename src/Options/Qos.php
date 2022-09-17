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

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setPrefetchSize(int $prefetchSize): void
    {
        $this->prefetchSize = $prefetchSize;
    }

    public function getPrefetchCount(): int
    {
        return $this->prefetchCount;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setPrefetchCount(int $prefetchCount): void
    {
        $this->prefetchCount = $prefetchCount;
    }

    /**
     * @deprecated
     */
    public function isGlobal(): bool
    {
        return false;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     *
     * @deprecated
     */
    public function setGlobal(bool $global): void
    {
    }
}
