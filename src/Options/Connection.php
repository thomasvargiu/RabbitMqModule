<?php

declare(strict_types=1);

namespace RabbitMqModule\Options;

use Laminas\Stdlib\AbstractOptions;

class Connection extends AbstractOptions
{
    /** @var string */
    protected $type = 'stream';

    /** @var string */
    protected $host = 'localhost';

    /** @var int */
    protected $port = 5672;

    /** @var string */
    protected $username = 'guest';

    /** @var string */
    protected $password = 'guest';

    /** @var string */
    protected $vhost = '/';

    /** @var bool */
    protected $insist = false;

    /** @var string */
    protected $loginMethod = 'AMQPLAIN';

    /** @var string */
    protected $locale = 'en_US';

    /** @var int */
    protected $readWriteTimeout = 3;

    /** @var bool */
    protected $keepAlive = false;

    /** @var int */
    protected $connectionTimeout = 3;

    /** @var int */
    protected $heartbeat = 0;

    /** @var array<string, mixed> */
    protected $sslOptions = [];

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getVhost(): string
    {
        return $this->vhost;
    }

    public function setVhost(string $vhost): void
    {
        $this->vhost = $vhost;
    }

    public function isInsist(): bool
    {
        return $this->insist;
    }

    public function setInsist(bool $insist): void
    {
        $this->insist = $insist;
    }

    public function getLoginMethod(): string
    {
        return $this->loginMethod;
    }

    public function setLoginMethod(string $loginMethod): void
    {
        $this->loginMethod = $loginMethod;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getReadWriteTimeout(): int
    {
        return $this->readWriteTimeout;
    }

    public function setReadWriteTimeout(int $readWriteTimeout): void
    {
        $this->readWriteTimeout = $readWriteTimeout;
    }

    public function isKeepAlive(): bool
    {
        return $this->keepAlive;
    }

    public function setKeepAlive(bool $keepAlive): void
    {
        $this->keepAlive = $keepAlive;
    }

    public function getConnectionTimeout(): int
    {
        return $this->connectionTimeout;
    }

    public function setConnectionTimeout(int $connectionTimeout): void
    {
        $this->connectionTimeout = $connectionTimeout;
    }

    public function getHeartbeat(): int
    {
        return $this->heartbeat;
    }

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
     * @param array<string, mixed> $sslOptions
     */
    public function setSslOptions(array $sslOptions): void
    {
        $this->sslOptions = $sslOptions;
    }
}
