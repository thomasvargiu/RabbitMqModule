<?php

namespace RabbitMqModule\Controller;

use Laminas\Mvc\Console\Controller\AbstractConsoleController as BaseController;
use Psr\Container\ContainerInterface;

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
