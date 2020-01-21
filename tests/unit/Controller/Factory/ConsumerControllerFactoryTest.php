<?php

namespace RabbitMqModule\Controller\Factory;

use Psr\Container\ContainerInterface;
use RabbitMqModule\Controller\ConsumerController;

class ConsumerControllerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $factory = new ConsumerControllerFactory();
        $controller = $factory($container);

        static::assertInstanceOf(ConsumerController::class, $controller);
    }
}
