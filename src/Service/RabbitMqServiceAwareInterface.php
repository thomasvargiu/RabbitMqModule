<?php

namespace RabbitMqModule\Service;

interface RabbitMqServiceAwareInterface
{
    /**
     * @return RabbitMqService
     */
    public function getRabbitMqService();

    /**
     * @param RabbitMqService $service
     * @return mixed
     */
    public function setRabbitMqService(RabbitMqService $service);
}
