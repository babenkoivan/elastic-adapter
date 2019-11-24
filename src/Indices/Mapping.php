<?php
declare(strict_types=1);

namespace ElasticAdaptor\Indices;

use BadMethodCallException;
use ElasticAdaptor\Support\ArrayableInterface;
use ElasticAdaptor\Support\Str;

final class Mapping implements ArrayableInterface
{
    /**
     * @var bool|null
     */
    private $isFieldNamesEnabled;
    /**
     * @var bool|null
     */
    private $isSourceEnabled;
    /**
     * @var array
     */
    private $properties = [];

    public function enableFieldNames(): self
    {
        $this->isFieldNamesEnabled = true;

        return $this;
    }

    public function disableFieldNames(): self
    {
        $this->isFieldNamesEnabled = false;

        return $this;
    }

    public function enableSource(): self
    {
        $this->isSourceEnabled = true;

        return $this;
    }

    public function disableSource(): self
    {
        $this->isSourceEnabled = false;

        return $this;
    }

    public function __call(string $method, array $arguments): self
    {
        if (count($arguments) == 0 || count($arguments) > 2) {
            throw new BadMethodCallException(sprintf('Invalid number of arguments for %s method', $method));
        }

        $property = ['type' => Str::toSnakeCase($method)];

        if (isset($arguments[1])) {
            $property += $arguments[1];
        }

        $this->properties[$arguments[0]] = $property;

        return $this;
    }

    public function toArray(): array
    {
        $mapping = [];

        if (isset($this->isFieldNamesEnabled)) {
            $mapping['_field_names'] = [
                'enabled' => $this->isFieldNamesEnabled
            ];
        }

        if (isset($this->isSourceEnabled)) {
            $mapping['_source'] = [
                'enabled' => $this->isSourceEnabled
            ];
        }

        if (count($this->properties) > 0) {
            $mapping['properties'] = $this->properties;
        }

        return $mapping;
    }
}
