<?php

/**
 * @codeCoverageIgnore
 */

namespace RabbitMqModule;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Class Module.
 *
 * @codeCoverageIgnore
 */
class Module implements ConfigProviderInterface
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
}
