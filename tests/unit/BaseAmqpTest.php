<?php

namespace RabbitMqModule;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Wire\AMQPTable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RabbitMqModule\Options\Exchange as ExchangeOptions;
use RabbitMqModule\Options\Queue as QueueOptions;

class BaseAmqpTest extends TestCase
{
    public function testConstructor()
    {
        $connection = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var MockObject|BaseAmqp $baseAmqp */
        $baseAmqp = $this->getMockBuilder(BaseAmqp::class)
            ->setConstructorArgs([$connection])
            ->setMethods(['__destruct'])
            ->getMock();

        $connection->expects(static::once())
            ->method('channel')
            ->willReturn($channel);

        static::assertEquals($channel, $baseAmqp->getChannel());
    }

    public function testSetChannel()
    {
        $connection = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var MockObject|BaseAmqp $baseAmqp */
        $baseAmqp = $this->getMockBuilder(BaseAmqp::class)
            ->setConstructorArgs([$connection])
            ->setMethods(['__destruct'])
            ->getMock();

        /** @var MockObject|AMQPChannel $channel */
        $baseAmqp->setChannel($channel);

        static::assertEquals($channel, $baseAmqp->getChannel());
    }

    public function testDestruct()
    {
        $connection = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $baseAmqp = $this->getMockBuilder(BaseAmqp::class)
            ->setConstructorArgs([$connection])
            ->setMethods(null)
            ->getMock();

        $connection->expects(static::once())
            ->method('isConnected')
            ->willReturn(true);

        $connection->expects(static::once())->method('close');
        $channel->expects(static::once())->method('close');

        /* @var BaseAmqp $baseAmqp */
        $baseAmqp->setChannel($channel);
        $baseAmqp->__destruct();
    }

    public function testReconnect()
    {
        $connection = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $baseAmqp = $this->getMockBuilder(BaseAmqp::class)
            ->setConstructorArgs([$connection])
            ->setMethods(['__destruct'])
            ->getMock();

        $connection->expects(static::once())->method('isConnected')->willReturn(true);
        $connection->expects(static::once())->method('reconnect');

        $baseAmqp->reconnect();
    }

    public function testReconnectWhenConnected()
    {
        $connection = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $baseAmqp = $this->getMockBuilder(BaseAmqp::class)
            ->setConstructorArgs([$connection])
            ->setMethods(['__destruct'])
            ->getMock();

        $connection->expects(static::once())->method('isConnected')->willReturn(false);
        $connection->expects(static::never())->method('reconnect');

        $baseAmqp->reconnect();
    }

    public function testQueueOptions()
    {
        $baseAmqp = $this->getMockBuilder(BaseAmqp::class)
            ->disableOriginalConstructor()
            ->setMethods(['__destruct'])
            ->getMock();

        $options = new QueueOptions();

        $baseAmqp->setQueueOptions($options);
        static::assertEquals($options, $baseAmqp->getQueueOptions());
    }

    public function testExchangeOptions()
    {
        $baseAmqp = $this->getMockBuilder(BaseAmqp::class)
            ->disableOriginalConstructor()
            ->setMethods(['__destruct'])
            ->getMock();

        $options = new ExchangeOptions();

        $baseAmqp->setExchangeOptions($options);
        static::assertEquals($options, $baseAmqp->getExchangeOptions());
    }

    /**
     * @param bool $isEnabled
     *
     * @dataProvider getDataProviderForAutoSetupFabricEnabled
     */
    public function testAutoSetupFabricEnabled(bool $isEnabled)
    {
        $baseAmqp = $this->getMockBuilder(BaseAmqp::class)
            ->disableOriginalConstructor()
            ->setMethods(['__destruct'])
            ->getMock();

        $baseAmqp->setAutoSetupFabricEnabled($isEnabled);
        static::assertEquals($isEnabled, $baseAmqp->isAutoSetupFabricEnabled());
    }

    public function getDataProviderForAutoSetupFabricEnabled(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @param array $exchangeData
     * @param array $queueData
     *
     * @dataProvider getDataProviderForSetupFabric
     */
    public function testSetupFabricWithoutBinds(array $exchangeData, array $queueData)
    {
        $channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->setMethods(['exchange_declare', 'queue_declare', 'queue_bind'])
            ->getMock();
        $channel->expects(static::once())
            ->method('exchange_declare')
            ->with(
                $exchangeData['name'],
                $exchangeData['type'],
                $exchangeData['passive'],
                $exchangeData['durable'],
                $exchangeData['auto_delete'],
                $exchangeData['internal'],
                $exchangeData['no_wait'],
                $exchangeData['arguments'],
                $exchangeData['ticket']
            );
        $channel->expects(static::once())
            ->method('queue_declare')
            ->with(
                $queueData['name'],
                $queueData['passive'],
                $queueData['durable'],
                $queueData['exclusive'],
                $queueData['auto_delete'],
                $queueData['no_wait'],
                new AMQPTable($queueData['arguments']),
                $queueData['ticket']
            )
            ->willReturn([$queueData['name']]);

        $routingKeys = $queueData['routing_keys'] ?? [''];
        $channel->expects(static::exactly(count($routingKeys)))
            ->method('queue_bind')
            ->withConsecutive(...array_map(function ($routingKey) use ($queueData, $exchangeData) {
                return [
                    $queueData['name'],
                    $exchangeData['name'],
                    $routingKey,
                ];
            }, $routingKeys));

        $baseAmqp = $this->getMockBuilder(BaseAmqp::class)
            ->disableOriginalConstructor()
            ->setMethods(['__destruct', 'getChannel'])
            ->getMock();
        $baseAmqp->expects(static::any())
            ->method('getChannel')
            ->willReturn($channel);

        $queueOptions = new QueueOptions($queueData);
        $baseAmqp->setQueueOptions($queueOptions);

        $exchangeOptions = new ExchangeOptions($exchangeData);
        $baseAmqp->setExchangeOptions($exchangeOptions);

        $baseAmqp->setupFabric();

        // declare only once
        $baseAmqp->setupFabric();
    }

    public function getDataProviderForSetupFabric(): array
    {
        return [
            [
                [
                    'declare'     => true,
                    'name'        => 'some_exchange_name',
                    'type'        => 'some_exchange_type',
                    'passive'     => true,
                    'durable'     => true,
                    'auto_delete' => true,
                    'internal'    => true,
                    'no_wait'     => true,
                    'arguments'   => [],
                    'ticket'      => 1,
                ],
                [
                    'name'         => 'some_queue_name',
                    'passive'      => true,
                    'durable'      => true,
                    'exclusive'    => true,
                    'auto_delete'  => true,
                    'no_wait'      => true,
                    'arguments'    => ['some_argument'],
                    'ticket'       => 1,
                    'routing_keys' => ['some_key'],
                ],
            ],
        ];
    }
}
