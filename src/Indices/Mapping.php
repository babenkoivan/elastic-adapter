<?php
declare(strict_types=1);

namespace ElasticAdaptor\Indices;

use BadMethodCallException;
use ElasticAdaptor\Support\Str;

final class Mapping
{
    /**
     * @var bool
     */
    private $isFieldNamesEnabled = true;
    /**
     * @var bool
     */
    private $isSourceEnabled = true;
    /**
     * @var array
     */
    private $properties = [];

    /**
     * @return $this
     */
    public function enableFieldNames(): self
    {
        $this->isFieldNamesEnabled = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableFieldNames(): self
    {
        $this->isFieldNamesEnabled = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableSource(): self
    {
        $this->isSourceEnabled = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableSource(): self
    {
        $this->isSourceEnabled = false;

        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments): self
    {
        if (count($arguments) > 2) {
            throw new BadMethodCallException(sprintf('Method %s doesn\'t exist', $name));
        }

        $property = ['type' => Str::toSnakeCase($name)];

        if (isset($arguments[1])) {
            $property += $arguments[1];
        }

        $this->properties[$arguments[0]] = $property;

        return $this;
    }
}
