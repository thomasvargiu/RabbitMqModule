# RabbitMqModule #

[![Build Status](https://scrutinizer-ci.com/g/thomasvargiu/RabbitMqModule/badges/build.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/RabbitMqModule/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/thomasvargiu/RabbitMqModule/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/RabbitMqModule/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thomasvargiu/RabbitMqModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thomasvargiu/RabbitMqModule/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/55300c0a10e7141211000b7d/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55300c0a10e7141211000b7d)

Integrates php-amqplib with Zend Framework 2 and RabbitMq.

Inspired from [RabbitMqBundle](https://github.com/videlalvaro/RabbitMqBundle/) for Symfony 2


**Status: development**

Not ready for production environment yet (see below).


## Usage ##

### Connections ###

You can configure multiple connections in configuration:

```php
return [
    'rabbitmq' => [
        'connection' => [
            // connection name
            'default' => [
                'type' => 'stream', // Available: stream, socket, ssl
                'host' => 'localhost',
                'username' => 'guest',
                'password' => 'guest',
                'vhost' => '/'
            ]
        ]
    ]
]
```

#### Options classes ####

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
            'producer-name' => [
                'connection' => 'default', // the connection name
                'exchange' => [
                    'type' => 'direct',
                    'name' => 'exchange-name'
                ],
                'queue' => [ // optional queue
                    'name' => 'queue-name' // can be an empty string,
                    'routing_keys' => [
                        // optional routing keys
                    ]
                ],
                'auto_setup_fabric_enabled' => true // auto-setup exchanges and queues 
            ]
        ]
    ]
]
```

#### Options classes ####

You can find all available options here:

- [Producer](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Producer.php)
- [Exchange](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Exchange.php)
- [Queue](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Queue.php)

#### Retrieve the service ####

You can retrieve the connection from service locator:

```php
// Getting a producer
/** @var \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator **/
/** @var \RabbitMqModule\ProducerInterface $producer **/
$producer = $serviceLocator->get('rabbitmq.producer.producer-name');
```


### Consumers ###

You can configure multiple consumers in configuration:

```php
return [
    'rabbitmq' => [
        'consumer' => [
            'consumer-name' => [
                'connection' => 'default', // the connection name
                'exchange' => [
                    'type' => 'direct',
                    'name' => 'exchange-name'
                ],
                'queue' => [
                    'name' => 'queue-name' // can be an empty string,
                    'routing_keys' => [
                        // optional routing keys
                    ]
                ],
                'auto_setup_fabric_enabled' => true, // auto-setup exchanges and queues
                'qos' => [
                    // optional QOS options for RabbitMQ
                ],
                'callback' => 'my-service-name',
            ]
        ]
    ]
]
```

#### Options classes ####

You can find all available options here:

- [Consumer](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Consumer.php)
- [Exchange](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Exchange.php)
- [Queue](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Queue.php)
- [Qos](https://github.com/thomasvargiu/RabbitMqModule/blob/master/src/Options/Qos.php)

#### Callback ####

The ```callback``` key must contain one of the following:

- A ```callable``: a closure or an invokable object that receive an ```PhpAmqpLib\Message\AMQPMessage``` object.
- An instance of ```RabbitMqModule\\ConsumerInterface```.
- A string service name in service locator (can be anything ```callable``` or an instance of ```RabbitMqModule\\ConsumerInterface```.

Take a look on ```RabbitMqModule\\ConsumerInterface``` class constants for available return values.

If your callback return ```false``` than the message will be rejected and requeued.

If your callback return anything else different from ```false``` and ```ConsumerInterface```constants, the default response is like ```MSG_ACK```constant.

#### Retrieve the service ####

You can retrieve the connection from service locator:

```php
// Getting a consumer
/** @var \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator **/
/** @var \RabbitMqModule\Consumer $consumer **/
$consumer = $serviceLocator->get('rabbitmq.consumer.consumer-name');

// Start consumer
$consumer->consume();
```


# Development status #

This module is not ready for production environment yet.

Why? It's not fully tested in AMQP environment and all things in my mind are not implemented yet.

## Missing things ##

- Console commands
- RPC services (client and server)
- Multiple consumer
- Something not in my mind yet
- Tests, tests, tests...
