<?php

namespace RabbitMqModule\Command;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class StartConsumerCommandTest extends TestCase
{
    public function testExecuteStartConsumerCommandWithInvalidTestConsumer(): void
    {
        $serviceManager = new ServiceManager();

        $command = new StartConsumerCommand($serviceManager);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'name' => 'foo']);

        $output = <<<'EOF'
Starting consumer foo
No consumer with name "foo" found

EOF;

        $this->assertEquals($output, $commandTester->getDisplay());
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }

    public function testExecuteStartConsumerCommandWithTestConsumer(): void
    {
        $consumer = $this->getMockBuilder('RabbitMqModule\Consumer')
            ->onlyMethods(['consume'])
            ->disableOriginalConstructor()
            ->getMock();

        $consumer
            ->expects(static::once())
            ->method('consume');

        $serviceManager = new ServiceManager();
        $serviceManager->setService('rabbitmq.consumer.foo', $consumer);

        $command = new StartConsumerCommand($serviceManager);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'name' => 'foo']);

        static::assertFalse(defined('AMQP_WITHOUT_SIGNALS'));
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testExecuteStartConsumerCommandWithTestConsumerWithoutSignals(): void
    {
        $consumer = $this->getMockBuilder('RabbitMqModule\Consumer')
            ->onlyMethods(['consume'])
            ->disableOriginalConstructor()
            ->getMock();

        $consumer
            ->expects(static::once())
            ->method('consume');

        $serviceManager = new ServiceManager();
        $serviceManager->setService('rabbitmq.consumer.foo', $consumer);

        $command = new StartConsumerCommand($serviceManager);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => 'foo',
            '--without-signals' => true,
        ]);

        static::assertTrue(defined('AMQP_WITHOUT_SIGNALS'));
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }

    /**
     * @throws ReflectionException
     */
    public function testStartConsumerCommandStopConsumer(): void
    {
        $consumer = $this->getMockBuilder('RabbitMqModule\Consumer')
            ->onlyMethods(['forceStopConsumer', 'stopConsuming'])
            ->disableOriginalConstructor()
            ->getMock();

        $consumer->expects(static::once())
            ->method('forceStopConsumer');

        $consumer->expects(static::once())
            ->method('stopConsuming');

        $container = $this->getMockBuilder('Laminas\ServiceManager\ServiceManager')
            ->getMock();

        $command = $this->getMockBuilder(StartConsumerCommand::class)
            ->setConstructorArgs([$container])
            ->onlyMethods(['callExit'])
            ->getMock();

        $command->expects(static::once())
            ->method('callExit');

        $property = (new ReflectionClass($command))->getProperty('consumer');
        $property->setAccessible(true);
        $property->setValue($command, $consumer);
        $property->setAccessible(false);

        $command->stopConsumer();
    }
}
