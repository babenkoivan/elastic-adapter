<?php declare(strict_types=1);

namespace ElasticAdapter\Indices;

use BadMethodCallException;
use ElasticAdapter\Support\ArrayableInterface;
use ElasticAdapter\Support\Str;

/**
 * @method $this alias(string $name, array $parameters = null)
 * @method $this binary(string $name, array $parameters = null)
 * @method $this boolean(string $name, array $parameters = null)
 * @method $this byte(string $name, array $parameters = null)
 * @method $this completion(string $name, array $parameters = null)
 * @method $this constantKeyword(string $name, array $parameters = null)
 * @method $this date(string $name, array $parameters = null)
 * @method $this dateNanos(string $name, array $parameters = null)
 * @method $this dateRange(string $name, array $parameters = null)
 * @method $this denseVector(string $name, array $parameters = null)
 * @method $this double(string $name, array $parameters = null)
 * @method $this doubleRange(string $name, array $parameters = null)
 * @method $this flattened(string $name, array $parameters = null)
 * @method $this float(string $name, array $parameters = null)
 * @method $this floatRange(string $name, array $parameters = null)
 * @method $this geoPoint(string $name, array $parameters = null)
 * @method $this geoShape(string $name, array $parameters = null)
 * @method $this halfFloat(string $name, array $parameters = null)
 * @method $this histogram(string $name)
 * @method $this integer(string $name, array $parameters = null)
 * @method $this integerRange(string $name, array $parameters = null)
 * @method $this ip(string $name, array $parameters = null)
 * @method $this ipRange(string $name, array $parameters = null)
 * @method $this join(string $name, array $parameters = null)
 * @method $this keyword(string $name, array $parameters = null)
 * @method $this long(string $name, array $parameters = null)
 * @method $this longRange(string $name, array $parameters = null)
 * @method $this nested(string $name, array $parameters = null)
 * @method $this object(string $name, array $parameters = null)
 * @method $this percolator(string $name)
 * @method $this rankFeature(string $name, array $parameters = null)
 * @method $this rankFeatures(string $name)
 * @method $this scaledFloat(string $name, array $parameters = null)
 * @method $this searchAsYouType(string $name, array $parameters = null)
 * @method $this shape(string $name, array $parameters = null)
 * @method $this short(string $name, array $parameters = null)
 * @method $this sparseVector(string $name)
 * @method $this text(string $name, array $parameters = null)
 * @method $this tokenCount(string $name, array $parameters = null)
 * @method $this wildcard(string $name, array $parameters = null)
 */
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
    /**
     * @var array
     */
    private $dynamicTemplates = [];

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

    public function dynamicTemplate(string $name, array $parameters): self
    {
        $this->dynamicTemplates[] = [$name => $parameters];
        return $this;
    }

    public function toArray(): array
    {
        $mapping = [];

        if (isset($this->isFieldNamesEnabled)) {
            $mapping['_field_names'] = [
                'enabled' => $this->isFieldNamesEnabled,
            ];
        }

        if (isset($this->isSourceEnabled)) {
            $mapping['_source'] = [
                'enabled' => $this->isSourceEnabled,
            ];
        }

        if (count($this->properties) > 0) {
            $mapping['properties'] = $this->properties;
        }

        if (count($this->dynamicTemplates) > 0) {
            $mapping['dynamic_templates'] = $this->dynamicTemplates;
        }

        return $mapping;
    }
}
