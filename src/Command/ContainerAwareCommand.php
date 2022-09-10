<?php

namespace RabbitMqModule\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

abstract class ContainerAwareCommand extends Command
{
    /** @var ContainerInterface */
    protected $container;

    final public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }
}
