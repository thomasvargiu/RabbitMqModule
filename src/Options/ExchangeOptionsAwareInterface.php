<?php

namespace RabbitMqModule\Options;

interface ExchangeOptionsAwareInterface
{
    /**
     * @return Queue
     */
    public function getExchangeOptions();
}
