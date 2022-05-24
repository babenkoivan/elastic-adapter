<?php declare(strict_types=1);

namespace Elastic\Adapter\Indices;

use BadMethodCallException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

/**
 * @method $this index(array $configuration)
 * @method $this analysis(array $configuration)
 */
final class Settings implements Arrayable
{
    private array $settings = [];

    public function __call(string $method, array $arguments): self
    {
        $argumentsCount = count($arguments);

        if ($argumentsCount === 0 || $argumentsCount > 1) {
            throw new BadMethodCallException(sprintf('Invalid number of arguments for %s method', $method));
        }

        $this->settings[Str::snake($method)] = $arguments[0];

        return $this;
    }

    public function toArray(): array
    {
        return $this->settings;
    }
}
