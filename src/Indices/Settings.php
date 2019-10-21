<?php
declare(strict_types=1);

namespace ElasticAdaptor\Indices;

use BadMethodCallException;
use ElasticAdaptor\Support\ArrayableInterface;
use ElasticAdaptor\Support\Str;

final class Settings implements ArrayableInterface
{
    /**
     * @var array
     */
    private $settings = [];

    /**
     * @param string $method
     * @param array $arguments
     * @return self
     */
    public function __call(string $method, array $arguments): self
    {
        if (count($arguments) == 0 || count($arguments) > 1) {
            throw new BadMethodCallException(sprintf('Invalid number of arguments for %s method', $method));
        }

        $this->settings[Str::toSnakeCase($method)] = $arguments[0];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->settings;
    }
}
