<?php

namespace RabbitMqModule\Command\Factory;

use Psr\Container\ContainerInterface;
use InvalidArgumentException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use RabbitMqModule\Command\ContainerAwareCommand;

class ContainerAwareCommandFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     *
     * @param array<mixed>|null $options
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): ContainerAwareCommand {
        if (! is_subclass_of($requestedName, ContainerAwareCommand::class)) {
            throw new InvalidArgumentException(
                sprintf(
                    "The '%s' must be an extension of %s",
                    $requestedName,
                    ContainerAwareCommand::class
                )
            );
        }

        return new $requestedName($container);
    }
}
