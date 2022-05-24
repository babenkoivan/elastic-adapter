<?php declare(strict_types=1);

namespace Elastic\Adapter\Indices;

final class Alias
{
    private string $name;
    private ?array $filter;
    private ?string $routing;

    public function __construct(string $name, ?array $filter = null, ?string $routing = null)
    {
        $this->name = $name;
        $this->filter = $filter;
        $this->routing = $routing;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function filter(): ?array
    {
        return $this->filter;
    }

    public function routing(): ?string
    {
        return $this->routing;
    }
}
