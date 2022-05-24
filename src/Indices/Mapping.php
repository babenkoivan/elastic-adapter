<?php declare(strict_types=1);

namespace Elastic\Adapter\Indices;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\ForwardsCalls;

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
 * @method $this nested(string $name, Closure|array $parameters = null)
 * @method $this object(string $name, Closure|array $parameters = null)
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
final class Mapping implements Arrayable
{
    use ForwardsCalls;

    private ?bool $isFieldNamesEnabled;
    private ?bool $isSourceEnabled;
    private MappingProperties $properties;
    private array $dynamicTemplates = [];

    public function __construct()
    {
        $this->properties = new MappingProperties();
    }

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

    public function dynamicTemplate(string $name, array $parameters): self
    {
        $this->dynamicTemplates[] = [$name => $parameters];
        return $this;
    }

    public function __call(string $method, array $parameters): self
    {
        $this->forwardCallTo($this->properties, $method, $parameters);
        return $this;
    }

    public function toArray(): array
    {
        $mapping = [];
        $properties = $this->properties->toArray();

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

        if (!empty($properties)) {
            $mapping['properties'] = $properties;
        }

        if (!empty($this->dynamicTemplates)) {
            $mapping['dynamic_templates'] = $this->dynamicTemplates;
        }

        return $mapping;
    }
}
