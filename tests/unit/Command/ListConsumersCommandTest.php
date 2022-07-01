<?php

namespace RabbitMqModule\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ListConsumersCommandTest extends TestCase
{
    public function testExecuteListCommandWithNoConfigKey(): void
    {
        $command = new ListConsumersCommand([]);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertEquals('No "rabbitmq.consumer" configuration key found!' . PHP_EOL, $commandTester->getDisplay());
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }

    public function testExecuteListCommandWithNoConsumers(): void
    {
        $command = new ListConsumersCommand([
            'rabbitmq' => [
                'consumer' => [],
            ],
        ]);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertEquals("No consumers defined!\n", $commandTester->getDisplay(true));
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testExecuteListCommand(): void
    {
        $command = new ListConsumersCommand([
            'rabbitmq' => [
                'consumer' => [
                    'consumer_key1' => [],
                    'consumer_key2' => ['description' => 'foo description'],
                ],
            ],
        ]);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $output = <<<'EOF'
- consumer_key1: 
- consumer_key2: foo description

EOF;

        $this->assertEquals($output, $commandTester->getDisplay(true));
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
