<?php

namespace RabbitMqModule\Options;

class QosTest extends \PHPUnit\Framework\TestCase
{
    public function testOptions()
    {
        $configuration = [
            'prefetch_size' => 7,
            'prefetch_count' => 6,
            'global' => true,
        ];
        $options = new Qos();
        $options->setFromArray($configuration);

        static::assertEquals(7, $options->getPrefetchSize());
        static::assertEquals(6, $options->getPrefetchCount());
        static::assertTrue($options->isGlobal());
    }
}
