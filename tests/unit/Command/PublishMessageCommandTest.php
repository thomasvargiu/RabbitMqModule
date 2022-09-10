<?php

namespace RabbitMqModule\Command;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
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
        $producer = $this->getMockBuilder('RabbitMqModule\Producer')
            ->onlyMethods(['publish'])
            ->disableOriginalConstructor()
            ->getMock();
        $producer
            ->expects(static::once())
            ->method('publish')
            ->with(
                static::equalTo('msg'),
                static::equalTo('bar')
            );

        $serviceManager = new ServiceManager();
        $serviceManager->setService('rabbitmq.producer.foo', $producer);

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

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
