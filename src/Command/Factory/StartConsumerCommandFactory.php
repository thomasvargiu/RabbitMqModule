<?php

declare(strict_types=1);

namespace RabbitMqModule\Command\Factory;

use Psr\Container\ContainerInterface;
use RabbitMqModule\Command\StartConsumerCommand;

final class StartConsumerCommandFactory
{
    public function __invoke(ContainerInterface $container): StartConsumerCommand
    {
        return new StartConsumerCommand($container);
    }
}
