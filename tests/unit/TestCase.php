<?php

declare(strict_types=1);

namespace RabbitMqModule;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

abstract class TestCase extends BaseTestCase
{
    use ProphecyTrait;
}
