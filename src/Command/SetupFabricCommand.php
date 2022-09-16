<?php

namespace RabbitMqModule\Command;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RabbitMqModule\ConfigProvider;
use RabbitMqModule\Service\SetupFabricAwareInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @psalm-import-type ConfigArray from ConfigProvider
 */
final class SetupFabricCommand extends Command
{
    public const NAME = 'rabbitmq:fabric:setup';

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct(self::NAME);
        $this->setDescription('Sets up the Rabbit MQ fabric');
        $this->container = $container;
    }

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
     * @psalm-return list<mixed>
     */
    private function getServiceParts(): array
    {
        /** @psalm-var ConfigArray $config */
        $config = $this->container->get('config');
        $serviceKeys = [
            'consumer',
            'producer',
            'rpc_client',
            'rpc_server',
        ];
        /** @psalm-var list<mixed> $parts */
        $parts = [];
        foreach ($serviceKeys as $serviceKey) {
            if (! isset($config['rabbitmq'][$serviceKey])) {
                throw new RuntimeException(
                    sprintf('No service "rabbitmq.%s" found in configuration', $serviceKey)
                );
            }

            $keys = array_keys($config['rabbitmq'][$serviceKey]);
            foreach ($keys as $key) {
                /** @psalm-suppress MixedAssignment */
                $parts[] = $this->container->get(sprintf('rabbitmq.%s.%s', $serviceKey, $key));
            }
        }

        return $parts;
    }
}
