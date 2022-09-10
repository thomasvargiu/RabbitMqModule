<?php

namespace RabbitMqModule\Command;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RabbitMqModule\Service\SetupFabricAwareInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class SetupFabricCommand extends ContainerAwareCommand
{
    /** @var string */
    protected static $defaultName = 'rabbitmq:fabric:setup';

    /** @var string */
    protected static $defaultDescription = 'Sets up the Rabbit MQ fabric';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Setting up the AMQP fabric</info>');

        try {
            $services = $this->getServiceParts();

            foreach ($services as $service) {
                if (! $service instanceof SetupFabricAwareInterface) {
                    continue;
                }
                $service->setupFabric();
            }
        } catch (Throwable $e) {
            $output->writeln("<error>Exception: {$e->getMessage()}</error>");

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return array<int, mixed>
     */
    protected function getServiceParts(): array
    {
        /** @var array<string, array<string, array<mixed>>> $config */
        $config = $this->container->get('config');
        $serviceKeys = [
            'consumer',
            'producer',
            'rpc_client',
            'rpc_server',
        ];
        $parts = [];
        foreach ($serviceKeys as $serviceKey) {
            if (! isset($config['rabbitmq'][$serviceKey])) {
                throw new RuntimeException(
                    sprintf('No service "rabbitmq.%s" found in configuration', $serviceKey)
                );
            }

            $keys = array_keys($config['rabbitmq'][$serviceKey]);
            foreach ($keys as $key) {
                $parts[] = $this->container->get(sprintf('rabbitmq.%s.%s', $serviceKey, $key));
            }
        }

        return $parts;
    }
}
