<?php

/**
 * @codeCoverageIgnore
 */

namespace RabbitMqModule;

use Laminas\Console\Adapter\AdapterInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Laminas\ModuleManager\Feature\ConsoleUsageProviderInterface;

/**
 * Class Module.
 *
 * @codeCoverageIgnore
 */
class Module implements
    ConfigProviderInterface,
    ConsoleUsageProviderInterface,
    ConsoleBannerProviderInterface
{
    /**
     * Returns configuration to merge with application configuration.
     *
     * @return array<mixed, mixed>
     */
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param AdapterInterface $console
     *
     * @return array<string, string>
     */
    public function getConsoleUsage(AdapterInterface $console): array
    {
        return [
            'rabbitmq setup-fabric' => 'Sets up the Rabbit MQ fabric',
            'rabbitmq list consumers' => 'List available consumers',
            'rabbitmq consumer <name> [--without-signals|-w]' => 'Start a consumer by name',
            'rabbitmq rpc_server <name> [--without-signals|-w]' => 'Start a rpc server by name',
            'rabbitmq stdin-producer <name> [--route=] <msg>' => 'Send a message with a producer',
        ];
    }

    /**
     * @param AdapterInterface $console
     *
     * @return string
     */
    public function getConsoleBanner(AdapterInterface $console): string
    {
        return 'RabbitMQ Module';
    }
}
