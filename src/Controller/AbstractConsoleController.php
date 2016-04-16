<?php

namespace RabbitMqModule\Controller;

use Zend\Mvc\Controller\AbstractConsoleController as BaseController;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractConsoleController extends BaseController
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $container;

    /**
     * ConsumerController constructor.
     *
     * @param ServiceLocatorInterface $container
     */
    public function __construct(ServiceLocatorInterface $container)
    {
        $this->container = $container;
    }
}
