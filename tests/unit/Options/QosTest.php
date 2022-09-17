<?php

namespace RabbitMqModule\Options;

class QosTest extends \RabbitMqModule\TestCase
{
    public function testOptions(): void
    {
        $configuration = [
            'prefetch_size' => 7,
            'prefetch_count' => 6,
        ];
        $options = new Qos();
        $options->setFromArray($configuration);

        static::assertEquals(7, $options->getPrefetchSize());
        static::assertEquals(6, $options->getPrefetchCount());
    }
}
