<?php

namespace RabbitMqModule\Controller;

use Zend\Console\ColorInterface;

/**
 * Class RpcServerController
 *
 * @package RabbitMqModule\Controller
 */
class RpcServerController extends AbstractConsoleController
{
    public function indexAction()
    {
        /** @var \Zend\Console\Request $request */
        $request = $this->getRequest();
        /** @var \Zend\Console\Response $response */
        $response = $this->getResponse();

        $this->getConsole()->writeLine(sprintf('Starting rpc server %s', $request->getParam('name')));

        $serviceName = sprintf('rabbitmq.rpc_server.%s', $request->getParam('name'));

        if (!$this->container->has($serviceName)) {
            $this->getConsole()->writeLine(
                sprintf('No rpc server with name "%s" found', $request->getParam('name')),
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
