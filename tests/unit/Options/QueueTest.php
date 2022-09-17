<?php

namespace RabbitMqModule\Options;

class QueueTest extends \RabbitMqModule\TestCase
{
    public function testOptions(): void
    {
        $configuration = [
            'name' => 'test-name',
            'passive' => true,
            'durable' => true,
            'auto_delete' => false,
            'exclusive' => true,
            'ticket' => 1,
            'arguments' => [
                'argument1' => 'value1',
            ],
            'routing_keys' => [
                'routing1',
                'routing2',
            ],
        ];
        $options = new Queue();
        $options->setFromArray($configuration);

        static::assertEquals($configuration['name'], $options->getName());
        static::assertEquals($configuration['passive'], $options->isPassive());
        static::assertEquals($configuration['durable'], $options->isDurable());
        static::assertEquals($configuration['auto_delete'], $options->isAutoDelete());
        static::assertEquals($configuration['exclusive'], $options->isExclusive());
        static::assertEquals($configuration['ticket'], $options->getTicket());
        static::assertEquals($configuration['arguments'], $options->getArguments());
        static::assertEquals($configuration['routing_keys'], $options->getRoutingKeys());
    }
}
