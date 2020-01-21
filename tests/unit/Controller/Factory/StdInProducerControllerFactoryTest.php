<?php

namespace RabbitMqModule\Controller\Factory;

use Psr\Container\ContainerInterface;
use RabbitMqModule\Controller\StdInProducerController;

class StdInProducerControllerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $factory = new StdInProducerControllerFactory();
        $controller = $factory($container);

        static::assertInstanceOf(StdInProducerController::class, $controller);
    }
}
