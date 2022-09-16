<?php
declare(strict_types=1);

namespace RabbitMqModule\Options;

use Laminas\Stdlib\AbstractOptions as BaseAbstractOptions;

/**
 * @internal
 */
abstract class AbstractOptions extends BaseAbstractOptions
{
    /**
     * Constructor
     *
     * @internal Use {@see static::fromArray}
     * @psalm-internal RabbitMqModule
     * @psalm-param array<string, mixed> $options
     */
    final public function __construct(array $options)
    {
        parent::__construct($options);
    }

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-param array<string, mixed> $data
     * @return AbstractOptions
     */
    public static function __set_state(array $data): AbstractOptions
    {
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (strncmp($key, '_', 1) === 0) {
                continue;
            }
            $filteredData[$key] = $value;
        }

        return new static($filteredData);
    }
}
