<?php

namespace RabbitMqModule\Command;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RabbitMqModule\Producer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class PublishMessageCommand extends Command
{
    public const NAME = 'rabbitmq:producer:publish';

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct(self::NAME);
        $this->setDescription('Send a message with a producer');
        $this->container = $container;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The producer name')
            ->addArgument('msg', InputArgument::REQUIRED, 'Message to publish')
            ->addOption(
                'route',
                'r',
                InputOption::VALUE_REQUIRED,
                'The routing key',
                ''
            );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $producerName */
        $producerName = $input->getArgument('name');
        /** @var string $msg */
        $msg = $input->getArgument('msg');
        /** @var string $route */
        $route = $input->getOption('route');

        $serviceName = "rabbitmq.producer.$producerName";

        if (! $this->container->has($serviceName)) {
            $output->writeln("<error>No producer with name \"$producerName\" found</error>");

            return Command::FAILURE;
        }

        /** @var Producer $producer */
        $producer = $this->container->get($serviceName);
        $producer->publish($msg, $route);

        return Command::SUCCESS;
    }
}
