<?php

namespace RabbitMqModule\Options;

interface QueueOptionsAwareInterface
{
    /**
     * @return Queue
     */
    public function getQueueOptions();
}
