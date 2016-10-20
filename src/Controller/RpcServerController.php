<?php

namespace RabbitMqModule\Controller;

use Zend\Console\ColorInterface;

/**
 * Class RpcServerController.
 */
class RpcServerController extends AbstractConsoleController
{
    public function indexAction()
    {
        /** @var \Zend\Console\Response $response */
        $response = $this->getResponse();

        $this->getConsole()->writeLine(sprintf('Starting rpc server %s', $this->params('name')));

        $serviceName = sprintf('rabbitmq.rpc_server.%s', $this->params('name'));

        if (!$this->container->has($serviceName)) {
            $this->getConsole()->writeLine(
                sprintf('No rpc server with name "%s" found', $this->params('name')),
                ColorInterface::RED
            );
            $response->setErrorLevel(1);

            return $response;
        }

        /** @var \RabbitMqModule\RpcServer $consumer */
        $consumer = $this->container->get($serviceName);
        $consumer->consume();

        return $response;
    }
}
