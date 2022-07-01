<?php

namespace RabbitMqModule\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListConsumersCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'rabbitmq:consumers:list';

    /** @var string */
    protected static $defaultDescription = 'List available consumers';

    /** @var array<string, array<string, mixed>> */
    protected $config;

    /**
     * @param array<string, array<string, mixed>> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! array_key_exists('rabbitmq', $this->config) || ! array_key_exists('consumer', $this->config['rabbitmq'])) {
            $output->writeln('<error>No "rabbitmq.consumer" configuration key found!</error>');

            return Command::FAILURE;
        }

        $consumers = $this->config['rabbitmq']['consumer'];

        if (! is_array($consumers) || count($consumers) === 0) {
            $output->writeln('<error>No consumers defined!</error>');

            return Command::SUCCESS;
        }

        foreach ($consumers as $name => $configuration) {
            $description = array_key_exists('description', $configuration) ? (string) $configuration['description'] : '';
            $output->writeln("- <info>$name</info>: <comment>$description</comment>");
        }

        return Command::SUCCESS;
    }
}
