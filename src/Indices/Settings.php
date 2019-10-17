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
     * @param string $name
     * @param array $arguments
     * @return self
     */
    public function __call(string $name, array $arguments): self
    {
        if (count($arguments) > 1) {
            throw new BadMethodCallException(sprintf('Method %s doesn\'t exist', $name));
        }

        $this->settings[Str::toSnakeCase($name)] = $arguments[0];

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
