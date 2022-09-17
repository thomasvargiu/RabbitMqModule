<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

/**
 * @psalm-type ConnectionOptions = array{
 *   type?: 'stream' | 'socket' | 'ssl' | 'lazy',
 *   host?: string,
 *   port?: int,
 *   username?: string,
 *   password?: string,
 *   vhost?: string,
 *   insist?: bool,
 *   loginMethod?: 'AMQPLAIN' | string,
 *   locale?: string,
 *   read_write_timeout?: int,
 *   keep_alive?: bool,
 *   connection_timeout?: int,
 *   heartbeat?: int
 * }
 */
final class Connection extends AbstractOptions
{
    protected string $type = 'stream';

    protected string $host = 'localhost';

    protected int $port = 5672;

    protected string $username = 'guest';

    protected string $password = 'guest';

    protected string $vhost = '/';

    protected bool $insist = false;

    protected string $loginMethod = 'AMQPLAIN';

    protected string $locale = 'en_US';

    protected int $readWriteTimeout = 3;

    protected bool $keepAlive = false;

    protected int $connectionTimeout = 3;

    protected int $heartbeat = 0;

    /** @var array<string, mixed> */
    protected array $sslOptions = [];

    /**
     * @psalm-param ConnectionOptions $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
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

    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getVhost(): string
    {
        return $this->vhost;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setVhost(string $vhost): void
    {
        $this->vhost = $vhost;
    }

    public function isInsist(): bool
    {
        return $this->insist;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setInsist(bool $insist): void
    {
        $this->insist = $insist;
    }

    public function getLoginMethod(): string
    {
        return $this->loginMethod;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setLoginMethod(string $loginMethod): void
    {
        $this->loginMethod = $loginMethod;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getReadWriteTimeout(): int
    {
        return $this->readWriteTimeout;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setReadWriteTimeout(int $readWriteTimeout): void
    {
        $this->readWriteTimeout = $readWriteTimeout;
    }

    public function isKeepAlive(): bool
    {
        return $this->keepAlive;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setKeepAlive(bool $keepAlive): void
    {
        $this->keepAlive = $keepAlive;
    }

    public function getConnectionTimeout(): int
    {
        return $this->connectionTimeout;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setConnectionTimeout(int $connectionTimeout): void
    {
        $this->connectionTimeout = $connectionTimeout;
    }

    public function getHeartbeat(): int
    {
        return $this->heartbeat;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     */
    public function setHeartbeat(int $heartbeat): void
    {
        $this->heartbeat = $heartbeat;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSslOptions(): array
    {
        return $this->sslOptions;
    }

    /**
     * @internal
     *
     * @psalm-internal RabbitMqModule
     *
     * @param array<string, mixed> $sslOptions
     */
    public function setSslOptions(array $sslOptions): void
    {
        $this->sslOptions = $sslOptions;
    }
}
