<?php

declare(strict_types=1);

namespace RabbitMqModule\Command\Factory;

use Psr\Container\ContainerInterface;
use RabbitMqModule\Command\StartRpcServerCommand;

final class StartRpcServerCommandFactory
{
    public function __invoke(ContainerInterface $container): StartRpcServerCommand
    {
        return new StartRpcServerCommand($container);
    }
}
