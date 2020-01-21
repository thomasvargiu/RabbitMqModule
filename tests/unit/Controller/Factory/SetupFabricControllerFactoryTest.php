<?php

namespace RabbitMqModule\Controller\Factory;

use Psr\Container\ContainerInterface;
use RabbitMqModule\Controller\SetupFabricController;

class SetupFabricControllerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $factory = new SetupFabricControllerFactory();
        $controller = $factory($container);

        static::assertInstanceOf(SetupFabricController::class, $controller);
    }
}
