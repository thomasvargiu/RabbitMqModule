<?php

namespace RabbitMqModule\Command\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PHPUnit\Framework\TestCase;
use RabbitMqModule\Command\PublishMessageCommand;
use RabbitMqModule\Command\SetupFabricCommand;
use RabbitMqModule\Command\StartConsumerCommand;
use RabbitMqModule\Command\StartRpcServerCommand;

class ContainerAwareCommandFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public function testFactory(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $factory = new ContainerAwareCommandFactory();

        $command = $factory($container, PublishMessageCommand::class);
        static::assertInstanceOf(PublishMessageCommand::class, $command);

        $command = $factory($container, SetupFabricCommand::class);
        static::assertInstanceOf(SetupFabricCommand::class, $command);

        $command = $factory($container, StartConsumerCommand::class);
        static::assertInstanceOf(StartConsumerCommand::class, $command);

        $command = $factory($container, StartRpcServerCommand::class);
        static::assertInstanceOf(StartRpcServerCommand::class, $command);
    }
}
