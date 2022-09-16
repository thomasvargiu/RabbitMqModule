<?php

declare(strict_types=1);

namespace RabbitMqModule\Command\Factory;

use Psr\Container\ContainerInterface;
use RabbitMqModule\Command\ListConsumersCommand;
use RabbitMqModule\ConfigProvider;

/**
 * @psalm-import-type ConfigArray from ConfigProvider
 */
final class ListConsumersCommandFactory
{
    public function __invoke(ContainerInterface $container): ListConsumersCommand
    {
        /** @psalm-var ConfigArray $config */
        $config = $container->get('config');

        return new ListConsumersCommand($config['rabbitmq']['consumer'] ?? []);
    }
}
