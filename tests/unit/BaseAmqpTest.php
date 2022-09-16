<?php

namespace RabbitMqModule;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Wire\AMQPTable;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use RabbitMqModule\Options\Exchange as ExchangeOptions;
use RabbitMqModule\Options\Queue as QueueOptions;
use ReflectionClass;

class BaseAmqpTest extends TestCase
{
    use ProphecyTrait;

    public function testConstructor(): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $baseAmqp = $this->getMockBuilder('RabbitMqModule\\BaseAmqp')
            ->setConstructorArgs([$connection])
            ->setMethods(['__destruct'])
            ->getMock();

        $baseAmqp->method('__destruct');

        $connection->expects(static::once())->method('channel')->willReturn($channel);

        /* @var \RabbitMqModule\BaseAmqp $baseAmqp */
        static::assertEquals($channel, $baseAmqp->getChannel());
    }

    public function testSetChannel(): void
    {
        $connection = $this->getMockBuilder('PhpAmqpLib\\Connection\\AbstractConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $channel = $this->getMockBuilder('PhpAmqpLib\\Channel\\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $baseAmqp = $this->getMockBuilder('RabbitMqModule\\BaseAmqp')
            ->setConstructorArgs([$connection])
            ->setMethods(['__destruct'])
            ->getMock();

        $baseAmqp->method('__destruct');

        /* @var \RabbitMqModule\BaseAmqp $baseAmqp */
        $baseAmqp->setChannel($channel);
        static::assertEquals($channel, $baseAmqp->getChannel());
    }

    protected static function getMethod($name)
    {
        $class = new ReflectionClass('RabbitMqModule\\BaseAmqp');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    public function testReconnect(): void
    {
        $connection = $this->prophesize(AbstractConnection::class);
        $baseAmqp = new BaseAmqpMock($connection->reveal());

        $connection->reconnect()->shouldBeCalled();

        $baseAmqp->reconnect();
    }

    public function testQueueOptions(): void
    {
        $connection = $this->prophesize(AbstractConnection::class);
        $baseAmqp = new BaseAmqpMock($connection->reveal());

        $options = new QueueOptions();

        $baseAmqp->setQueueOptions($options);
        static::assertEquals($options, $baseAmqp->getQueueOptions());
    }

    public function testExchangeOptions(): void
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
     * @dataProvider getDataProviderForAutoSetupFabricEnabled
     */
    public function testAutoSetupFabricEnabled(bool $isEnabled): void
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
     * @dataProvider getDataProviderForSetupFabric
     */
    public function testSetupFabricWithoutBinds(array $exchangeData, array $queueData): void
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

        $connection = $this->prophesize(AbstractConnection::class);
        $baseAmqp = new BaseAmqpMock($connection->reveal());
        $baseAmqp->setChannel($channel);

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
                    'declare' => true,
                    'name' => 'some_exchange_name',
                    'type' => 'some_exchange_type',
                    'passive' => true,
                    'durable' => true,
                    'auto_delete' => true,
                    'internal' => true,
                    'no_wait' => true,
                    'arguments' => [],
                    'ticket' => 1,
                ],
                [
                    'name' => 'some_queue_name',
                    'passive' => true,
                    'durable' => true,
                    'exclusive' => true,
                    'auto_delete' => true,
                    'no_wait' => true,
                    'arguments' => ['some_argument'],
                    'ticket' => 1,
                    'routing_keys' => ['some_key'],
                ],
            ],
        ];
    }
}
