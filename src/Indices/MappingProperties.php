<?php declare(strict_types=1);

namespace Elastic\Adapter\Indices;

use BadMethodCallException;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

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
final class MappingProperties implements Arrayable
{
    private array $properties = [];

    /**
     * @param Closure|array $parameters
     */
    public function object(string $name, $parameters = null): self
    {
        $this->properties[$name] = ['type' => 'object'];

        if (isset($parameters)) {
            $this->properties[$name] += $this->normalizeParametersWithProperties($parameters);
        }

        return $this;
    }

    /**
     * @param Closure|array $parameters
     */
    public function nested(string $name, $parameters = null): self
    {
        $this->properties[$name] = ['type' => 'nested'];

        if (isset($parameters)) {
            $this->properties[$name] += $this->normalizeParametersWithProperties($parameters);
        }

        return $this;
    }

    public function __call(string $method, array $arguments): self
    {
        $argumentsCount = count($arguments);

        if ($argumentsCount === 0 || $argumentsCount > 2) {
            throw new BadMethodCallException(sprintf('Invalid number of arguments for %s method', $method));
        }

        $property = ['type' => Str::snake($method)];

        if (isset($arguments[1])) {
            $property += $arguments[1];
        }

        $this->properties[$arguments[0]] = $property;

        return $this;
    }

    public function toArray(): array
    {
        return $this->properties;
    }

    /**
     * @param Closure|array $parameters
     */
    private function normalizeParametersWithProperties($parameters): array
    {
        if ($parameters instanceof Closure) {
            $parameters = $parameters(new self());
        }

        if ($parameters['properties'] instanceof self) {
            $parameters['properties'] = $parameters['properties']->toArray();
        }

        return $parameters;
    }
}
