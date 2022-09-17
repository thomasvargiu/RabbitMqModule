<?php

namespace RabbitMqModule\Command;

use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use RabbitMqModule\ProducerInterface;
use RabbitMqModule\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class PublishMessageCommandTest extends TestCase
{
    public function testExecutePublishMessageCommandWithInvalidTestProducer(): void
    {
        $serviceManager = new ServiceManager();

        $command = new PublishMessageCommand($serviceManager);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => 'foo',
            'msg' => 'msg',
            '--route' => 'bar',
        ]);

        $this->assertEquals('No producer with name "foo" found' . PHP_EOL, $commandTester->getDisplay());
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }

    public function testExecutePublishMessageCommandWithTestProducer(): void
    {
        $producer = $this->prophesize(ProducerInterface::class);
        $producer->publish('msg', 'bar')->shouldBeCalledOnce();

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('rabbitmq.producer.foo')->willReturn(true);
        $container->get('rabbitmq.producer.foo')->willReturn($producer->reveal());

        $command = new PublishMessageCommand($container->reveal());

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => 'foo',
            'msg' => 'msg',
            '--route' => 'bar',
        ]);

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
