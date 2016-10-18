<?php

namespace RabbitMqModule\Controller;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractConsoleController as BaseController;

class AbstractConsoleController extends BaseController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ConsumerController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
