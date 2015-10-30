<?php

namespace RabbitMqModule\Controller;

use PhpAmqpLib\Exception\AMQPTimeoutException;
use Zend\Console\ColorInterface;
use Zend\Mvc\Controller\AbstractConsoleController;
use RabbitMqModule\Consumer;

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

        if (!$this->getServiceLocator()->has($serviceName)) {
            $this->getConsole()->writeLine(
                sprintf('No consumer with name "%s" found', $request->getParam('name')),
                ColorInterface::RED
            );
            $response->setErrorLevel(1);

            return $response;
        }

        /* @var \RabbitMqModule\Consumer $consumer */
        $this->consumer = $this->getServiceLocator()->get($serviceName);
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
