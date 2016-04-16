<?php

namespace RabbitMqModule\Controller;

use PhpAmqpLib\Exception\AMQPTimeoutException;
use Zend\Console\ColorInterface;
use RabbitMqModule\Consumer;

/**
 * Class ConsumerController
 *
 * @package RabbitMqModule\Controller
 */
class ConsumerController extends AbstractConsoleController
{
    /**
     * @var Consumer
     */
    protected $consumer;

    public function indexAction()
    {
        /** @var \Zend\Console\Request $request */
        $request = $this->getRequest();
        /** @var \Zend\Console\Response $response */
        $response = $this->getResponse();

        $this->getConsole()->writeLine(sprintf('Starting consumer %s', $request->getParam('name')));

        $withoutSignals = $request->getParam('without-signals') || $request->getParam('w');

        $serviceName = sprintf('rabbitmq.consumer.%s', $request->getParam('name'));

        if (!$this->container->has($serviceName)) {
            $this->getConsole()->writeLine(
                sprintf('No consumer with name "%s" found', $request->getParam('name')),
                ColorInterface::RED
            );
            $response->setErrorLevel(1);

            return $response;
        }

        /* @var \RabbitMqModule\Consumer $consumer */
        $this->consumer = $this->container->get($serviceName);
        $this->consumer->setSignalsEnabled(!$withoutSignals);

        if ($withoutSignals) {
            define('AMQP_WITHOUT_SIGNALS', true);
        }

        // @codeCoverageIgnoreStart
        if (!$withoutSignals && extension_loaded('pcntl')) {
            if (!function_exists('pcntl_signal')) {
                throw new \BadFunctionCallException(
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
     * List available consumers
     *
     * @return string
     */
    public function listAction()
    {
        /** @var array $config */
        $config = $this->container->get('Configuration');

        if (!array_key_exists('rabbitmq', $config) || !array_key_exists('consumer', $config['rabbitmq'])) {
            return 'No \'rabbitmq.consumer\' configuration key found!';
        }

        $consumers = $config['rabbitmq']['consumer'];

        if (!is_array($consumers) || count($consumers) === 0) {
            return 'No consumers defined!';
        }

        foreach ($consumers as $name => $configuration) {
            $description = array_key_exists('description', $configuration) ? (string)$configuration['description'] : '';
            $this->getConsole()->writeLine(sprintf(
                '- %s: %s',
                $this->getConsole()->colorize($name, ColorInterface::LIGHT_GREEN),
                $this->getConsole()->colorize($description, ColorInterface::LIGHT_YELLOW)
            ));
        }
    }

    /**
     * Stop consumer.
     */
    public function stopConsumer()
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
     * @param Consumer $consumer
     *
     * @return $this
     */
    public function setConsumer(Consumer $consumer)
    {
        $this->consumer = $consumer;

        return $this;
    }

    /**
     * @param int $code
     * @codeCoverageIgnore
     */
    protected function callExit($code)
    {
        exit($code);
    }
}
