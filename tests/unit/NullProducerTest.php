<?php

namespace RabbitMqModule;

use PHPUnit\Framework\TestCase;

class NullProducerTest extends TestCase
{
    public function testInstanceOfProducerInterface(): void
    {
        self::assertInstanceOf(ProducerInterface::class, new NullProducer());
    }
}
