<?php

declare(strict_types=1);

namespace RabbitMqModule\Command\Factory;

use Psr\Container\ContainerInterface;
use RabbitMqModule\Command\SetupFabricCommand;

final class SetupFabricCommandFactory
{
    public function __invoke(ContainerInterface $container): SetupFabricCommand
    {
        return new SetupFabricCommand($container);
    }
}
