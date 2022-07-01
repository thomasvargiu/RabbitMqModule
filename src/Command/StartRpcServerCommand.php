<?php

namespace RabbitMqModule\Command;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RabbitMqModule\RpcServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartRpcServerCommand extends ContainerAwareCommand
{
    /** @var string */
    protected static $defaultName = 'rabbitmq:rpc-server:start';

    /** @var string */
    protected static $defaultDescription = 'Start a rpc server by name';

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The RpcServer name');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $consumerName */
        $consumerName = $input->getArgument('name');

        $output->writeln("<info>Starting rpc server $consumerName</info>");

        $serviceName = "rabbitmq.rpc_server.$consumerName";

        if (! $this->container->has($serviceName)) {
            $output->writeln("<error>No rpc server with name \"$consumerName\" found</error>");

            return Command::FAILURE;
        }

        /** @var RpcServer $consumer */
        $consumer = $this->container->get($serviceName);
        $consumer->consume();

        return Command::SUCCESS;
    }
}
