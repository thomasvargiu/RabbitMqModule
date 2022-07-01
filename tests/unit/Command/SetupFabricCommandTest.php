<?php

namespace RabbitMqModule\Command;

use ArrayObject;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class SetupFabricCommandTest extends TestCase
{
    public function testExecuteSetupFabricCommandWithInvalidConfigKeys(): void
    {
        $serviceManager = new ServiceManager();

        $command = new SetupFabricCommand($serviceManager);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }

    public function testExecuteSetupFabricCommand(): void
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('config', [
            'rabbitmq' => [
                'consumer' => [
                    'foo-consumer1' => [],
                    'foo-consumer2' => [],
                ],
                'producer' => [
                    'bar-producer1' => [],
                    'bar-producer2' => [],
                    'bar-producer-fake' => [],
                ],
                'rpc_server' => [],
                'rpc_client' => [],
            ],
        ]);

        $service = $this->getMockBuilder('RabbitMqModule\\Service\\SetupFabricAwareInterface')
            ->getMockForAbstractClass();
        $service->expects(static::exactly(4))
            ->method('setupFabric');
        $someOtherService = new ArrayObject();
        $serviceManager->setService('rabbitmq.consumer.foo-consumer1', $service);
        $serviceManager->setService('rabbitmq.consumer.foo-consumer2', $service);
        $serviceManager->setService('rabbitmq.producer.bar-producer1', $service);
        $serviceManager->setService('rabbitmq.producer.bar-producer2', $service);
        $serviceManager->setService('rabbitmq.producer.bar-producer-fake', $someOtherService);

        $command = new SetupFabricCommand($serviceManager);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
