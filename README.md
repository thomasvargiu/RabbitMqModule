# RabbitMqModule #

[![Build Status](https://travis-ci.org/thomasvargiu/RabbitMqModule.svg?branch=master)](https://travis-ci.org/thomasvargiu/RabbitMqModule)
[![Code Coverage](https://scrutinizer-ci.com/g/thomasvargiu/RabbitMqModule/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/RabbitMqModule/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thomasvargiu/RabbitMqModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/RabbitMqModule/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/55300c0a10e7141211000b7d/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55300c0a10e7141211000b7d)

Integrates php-amqplib with Zend Framework and RabbitMq.

Inspired from [RabbitMqBundle](https://github.com/videlalvaro/RabbitMqBundle/) for Symfony 2

## Usage ##

### Connections ###

You can configure multiple connections in configuration:

```php
return [
    'rabbitmq' => [
        'connection' => [
            // connection name
            'default' => [ // default values
                'type' => 'stream', // Available: stream, socket, ssl, lazy
                'host' => 'localhost',
                'port' => 5672,
                'username' => 'guest',
                'password' => 'guest',
                'vhost' => '/',
                'insist' => false,
                'read_write_timeout' => 2,
                'keep_alive' => false,
                'connection_timeout' => 3,
                'heartbeat' => 0
            ]
        ]
    ]
]
```

#### Option classes ####

You can find all available options here:

- [Connection](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Connection.php)


#### Retrieve the service ####

You can retrieve the connection from service locator:

```php
// Getting the 'default' connection
/** @var \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator **/
$connection = $serviceLocator->get('rabbitmq.connection.default');
```


### Producers ###

You can configure multiple producers in configuration:

```php
return [
    'rabbitmq' => [
        'producer' => [
            'producer_name' => [
                'connection' => 'default', // the connection name
                'exchange' => [
                    'type' => 'direct',
                    'name' => 'exchange-name',
                    'durable' => true,      // (default)
                    'auto_delete' => false, // (default)
                    'internal' => false,    // (default)
                    'no_wait' => false,     // (default)
                    'declare' => true,      // (default)
                    'arguments' => [],      // (default)
                    'ticket' => 0,          // (default)
                    'exchange_binds' => []  // (default)
                ],
                'queue' => [ // optional queue
                    'name' => 'queue-name', // can be an empty string,
                    'type' => null,         // (default)
                    'passive' => false,     // (default)
                    'durable' => true,      // (default)
                    'auto_delete' => false, // (default)
                    'exclusive' => false,   // (default)
                    'no_wait' => false,     // (default)
                    'arguments' => [],      // (default)
                    'ticket' => 0,          // (default)
                    'routing_keys' => []    // (default)
                ],
                'auto_setup_fabric_enabled' => true // auto-setup exchanges and queues 
            ]
        ]
    ]
]
```

#### Option classes ####

You can find all available options here:

- [Producer](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Producer.php)
- [Exchange](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Exchange.php)
- [Queue](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Queue.php)

#### Retrieve the service ####

You can retrieve the producer from service locator:

```php
// Getting a producer
/** @var \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator **/
/** @var \RabbitMqModule\ProducerInterface $producer **/
$producer = $serviceLocator->get('rabbitmq.producer.producer_name');

// Sending a message
$producer->publish(json_encode(['foo' => 'bar']));
```


### Consumers ###

You can configure multiple consumers in configuration:

```php
return [
    'rabbitmq' => [
        'consumer' => [
            'consumer_name' => [
                'description' => 'Consumer description',
                'connection' => 'default', // the connection name
                'exchange' => [
                    'type' => 'direct',
                    'name' => 'exchange-name'
                ],
                'queue' => [
                    'name' => 'queue-name', // can be an empty string,
                    'routing_keys' => [
                        // optional routing keys
                    ]
                ],
                'auto_setup_fabric_enabled' => true, // auto-setup exchanges and queues
                'qos' => [
                    // optional QOS options for RabbitMQ
                    'prefetch_size' => 0,
                    'prefetch_count' => 1,
                    'global' => false
                ],
                'callback' => 'my-service-name',
            ]
        ]
    ]
]
```

#### Option classes ####

You can find all available options here:

- [Consumer](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Consumer.php)
- [Exchange](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Exchange.php)
- [Queue](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Queue.php)
- [Qos](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Qos.php)

#### Callback ####

The ```callback``` key must contain one of the following:

- A ```callable```: a closure or an invokable object that receive an ```PhpAmqpLib\Message\AMQPMessage``` object.
- An instance of ```RabbitMqModule\\ConsumerInterface```.
- A string service name in service locator (can be anything ```callable``` or an instance of ```RabbitMqModule\\ConsumerInterface```.

Take a look on ```RabbitMqModule\\ConsumerInterface``` class constants for available return values.

If your callback return ```false``` than the message will be rejected and requeued.

If your callback return anything else different from ```false``` and one of ```ConsumerInterface```constants, the default response is like ```MSG_ACK```constant.

#### Retrieve the service ####

You can retrieve the consumer from service locator:

```php
// Getting a consumer
/** @var \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator **/
/** @var \RabbitMqModule\Consumer $consumer **/
$consumer = $serviceLocator->get('rabbitmq.consumer.consumer_name');

// Start consumer
$consumer->consume();
```

There is a console command available to list and start consumers. See below.

#### Consumer Example ####

```php
use PhpAmqpLib\Message\AMQPMessage;
use RabbitMqModule\ConsumerInterface;

class FetchProposalsConsumer implements ConsumerInterface
{
    /**
     * @param AMQPMessage $message
     *
     * @return int
     */
    public function execute(AMQPMessage $message)
    {
        $data = json_decode($message->body, true);

        try {
            // do something...
        } catch (\PDOException $e) {
            return ConsumerInterface::MSG_REJECT_REQUEUE;
        } catch (\Exception $e) {
            return ConsumerInterface::MSG_REJECT;
        }

        return ConsumerInterface::MSG_ACK;
    }
}


```

## Exchange2exchange binding

You can configure exchange2exchange binding in producers or consumers.
Example:

```php
return [
    'rabbitmq' => [
        'consumer' => [
            'consumer_name' => [
                // ...
                'exchange' => [
                    'type' => 'fanout',
                    'name' => 'exchange_to_bind_to',
                    'exchange_binds' => [
                        [
                            'exchange' => [
                                'type' => 'fanout',
                                'name' => 'main_exchange'
                            ],
                            'routing_keys' => [
                                '#'
                            ]
                        ]
                    ]
                ],
            ]
        ]
    ]
]
```


## Console usage ##

There are some console commands available:

- ```rabbitmq setup-fabric```: Setup fabric for each service, declaring exchanges and queues
- ```rabbitmq list consumers```: List available consumers
- ```rabbitmq consumer <name> [--without-signals|-w]```: Start a consumer by name
- ```rabbitmq rpc_server <name> [--without-signals|-w]```: Start a rpc server by name
- ```rabbitmq stdin-producer <name> [--route=] <msg>```: Send a message with a producer

