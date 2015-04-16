<?php

namespace RabbitMqModule\Service;

trait RabbitMqServiceAwareTrait
{
    /**
     * @var RabbitMqService
     */
    protected $rabbitMqService;

    /**
     * @return RabbitMqService
     */
    public function getRabbitMqService()
    {
        return $this->rabbitMqService;
    }

    /**
     * @param RabbitMqService $rabbitMqService
     * @return $this
     */
    public function setRabbitMqService(RabbitMqService $rabbitMqService)
    {
        $this->rabbitMqService = $rabbitMqService;
        return $this;
    }
}
