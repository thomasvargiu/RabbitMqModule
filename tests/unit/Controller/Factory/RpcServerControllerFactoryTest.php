<?php

namespace RabbitMqModule\Controller\Factory;

use Psr\Container\ContainerInterface;
use RabbitMqModule\Controller\RpcServerController;

class RpcServerControllerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $factory = new RpcServerControllerFactory();
        $controller = $factory($container);

        static::assertInstanceOf(RpcServerController::class, $controller);
    }
}
