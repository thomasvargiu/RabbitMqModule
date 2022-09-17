<?php

namespace RabbitMqModule\Command;

use RabbitMqModule\Options\Consumer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @psalm-import-type ConsumerOptions from Consumer
 */
final class ListConsumersCommand extends Command
{
    public const NAME = 'rabbitmq:consumers:list';

    /** @psalm-var array<string, ConsumerOptions> */
    private array $consumers;

    /**
     * @psalm-param array<string, ConsumerOptions> $consumers
     */
    public function __construct(array $consumers)
    {
        $this->consumers = $consumers;
        parent::__construct(self::NAME);
        $this->setDescription('List available consumers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (empty($this->consumers)) {
            $output->writeln('<error>No consumers defined!</error>');

            return Command::SUCCESS;
        }

        foreach ($this->consumers as $name => $configuration) {
            $description = $configuration['description'] ?? '';
            $output->writeln("- <info>$name</info>: <comment>$description</comment>");
        }

        return Command::SUCCESS;
    }
}
