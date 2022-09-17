<?php

namespace RabbitMqModule;

class NullProducerTest extends TestCase
{
    public function testInstanceOfProducerInterface(): void
    {
        self::assertInstanceOf(ProducerInterface::class, new NullProducer());
    }
}
