<?php

namespace RabbitMqModule\Controller;

use Zend\Console\ColorInterface;
use Zend\Mvc\Controller\AbstractConsoleController;

class ConsumerController extends AbstractConsoleController
{

    public function indexAction()
    {
        /** @var \Zend\Console\Request $request */
        $request = $this->getRequest();
        /** @var \Zend\Console\Response $response */
        $response = $this->getResponse();

        $this->getConsole()->writeLine(sprintf('Starting consumer %s', $request->getParam('name')));

        $serviceName = sprintf('rabbitmq.consumer.%s', $request->getParam('name'));

        if (!$this->getServiceLocator()->has($serviceName)) {
            $this->getConsole()->writeLine(
                sprintf('No consumer with name "%s" found', $request->getParam('name')),
                ColorInterface::RED
            );
            $response->setErrorLevel(1);
            return $response;
        }

        /** @var \RabbitMqModule\Consumer $consumer */
        $consumer = $this->getServiceLocator()->get($serviceName);
        $consumer->consume();

        return $response;
    }
}
