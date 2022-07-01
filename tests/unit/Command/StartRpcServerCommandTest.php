<?php

namespace RabbitMqModule\Command;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class StartRpcServerCommandTest extends TestCase
{
    public function testExecuteStartRpcServerCommandWithInvalidTestRpcServer(): void
    {
        $serviceManager = new ServiceManager();

        $command = new StartRpcServerCommand($serviceManager);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'name' => 'foo']);

        $output = <<<'EOF'
Starting rpc server foo
No rpc server with name "foo" found

EOF;

        $this->assertEquals($output, $commandTester->getDisplay());
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }

    public function testExecuteStartRpcServerCommandWithTestRpcServer(): void
    {
        $rpcServer = $this->getMockBuilder('RabbitMqModule\RpcServer')
            ->onlyMethods(['consume'])
            ->disableOriginalConstructor()
            ->getMock();

        $rpcServer
            ->expects(static::once())
            ->method('consume');

        $serviceManager = new ServiceManager();
        $serviceManager->setService('rabbitmq.rpc_server.foo', $rpcServer);

        $command = new StartRpcServerCommand($serviceManager);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'name' => 'foo']);

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
