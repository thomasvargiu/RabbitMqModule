<?php

declare(strict_types=1);

namespace RabbitMqModule\Command\Factory;

use Psr\Container\ContainerInterface;
use RabbitMqModule\Command\PublishMessageCommand;

final class PublishMessageCommandFactory
{
    public function __invoke(ContainerInterface $container): PublishMessageCommand
    {
        return new PublishMessageCommand($container);
    }
}
