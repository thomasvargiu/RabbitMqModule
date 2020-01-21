<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use Laminas\Stdlib\AbstractOptions;

class Qos extends AbstractOptions
{
    /** @var int */
    protected $prefetchSize = 0;

    /** @var int */
    protected $prefetchCount = 0;

    /** @var bool */
    protected $global = false;

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

    public function isGlobal(): bool
    {
        return $this->global;
    }

    /**
     * @param bool $global
     */
    public function setGlobal($global): void
    {
        $this->global = $global;
    }
}
