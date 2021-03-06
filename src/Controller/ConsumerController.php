<?php

declare(strict_types=1);

namespace RabbitMqModule\Controller;

use BadFunctionCallException;
use function extension_loaded;
use function function_exists;
use Laminas\Console\ColorInterface;
use Laminas\Console\Response;
use function pcntl_signal;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use RabbitMqModule\Consumer;

/**
 * Class ConsumerController.
 */
class ConsumerController extends AbstractConsoleController
{
    /** @var Consumer */
    protected $consumer;

    public function indexAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        $this->getConsole()->writeLine(sprintf('Starting consumer %s', $this->params('name')));

        $withoutSignals = $this->params('without-signals') || $this->params('w');

        $serviceName = sprintf('rabbitmq.consumer.%s', $this->params('name'));

        if (! $this->container->has($serviceName)) {
            $this->getConsole()->writeLine(
                sprintf('No consumer with name "%s" found', $this->params('name')),
                ColorInterface::RED
            );
            $response->setErrorLevel(1);

            return $response;
        }

        /* @var \RabbitMqModule\Consumer $consumer */
        $this->consumer = $this->container->get($serviceName);
        $this->consumer->setSignalsEnabled(! $withoutSignals);

        if ($withoutSignals) {
            define('AMQP_WITHOUT_SIGNALS', true);
        }

        // @codeCoverageIgnoreStart
        if (! $withoutSignals && extension_loaded('pcntl')) {
            if (! function_exists('pcntl_signal')) {
                throw new BadFunctionCallException(
                    'Function \'pcntl_signal\' is referenced in the php.ini \'disable_functions\' and can\'t be called.'
                );
            }

            pcntl_signal(SIGTERM, [$this, 'stopConsumer']);
            pcntl_signal(SIGINT, [$this, 'stopConsumer']);
        }
        // @codeCoverageIgnoreEnd

        $this->consumer->consume();

        return $response;
    }

    /**
     * List available consumers.
     */
    public function listAction(): Response
    {
        /** @var array<string, mixed> $config */
        $config = $this->container->get('config');
        /** @var Response $response */
        $response = $this->getResponse();

        if (! array_key_exists('rabbitmq', $config) || ! array_key_exists('consumer', $config['rabbitmq'])) {
            $response->setErrorLevel(1);
            $this->getConsole()->writeText('No "rabbitmq.consumer" configuration key found!', ColorInterface::LIGHT_RED);

            return $response;
        }

        $consumers = $config['rabbitmq']['consumer'];

        if (! is_array($consumers) || count($consumers) === 0) {
            $this->getConsole()->writeText('No consumers defined!', ColorInterface::LIGHT_RED);

            return $response;
        }

        foreach ($consumers as $name => $configuration) {
            $description = array_key_exists('description', $configuration) ? (string) $configuration['description'] : '';
            $this->getConsole()->writeLine(sprintf(
                '- %s: %s',
                $this->getConsole()->colorize($name, ColorInterface::LIGHT_GREEN),
                $this->getConsole()->colorize($description, ColorInterface::LIGHT_YELLOW)
            ));
        }

        return $response;
    }

    /**
     * Stop consumer.
     */
    public function stopConsumer(): void
    {
        if ($this->consumer instanceof Consumer) {
            $this->consumer->forceStopConsumer();
            try {
                $this->consumer->stopConsuming();
            } catch (AMQPTimeoutException $e) {
                // ignore
            }
        }
        $this->callExit(0);
    }

    /**
     * @return $this
     */
    public function setConsumer(Consumer $consumer): self
    {
        $this->consumer = $consumer;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function callExit(int $code): void
    {
        exit($code);
    }
}
