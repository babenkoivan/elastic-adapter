<?php declare(strict_types=1);

namespace Elastic\Adapter\Indices;

final class Alias
{
    private string $name;
    private bool $isWriteIndex;
    private ?array $filter;
    private ?string $routing;

    public function __construct(
        string $name,
        bool $isWriteIndex = false,
        ?array $filter = null,
        ?string $routing = null
    ) {
        $this->name = $name;
        $this->isWriteIndex = $isWriteIndex;
        $this->filter = $filter;
        $this->routing = $routing;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function isWriteIndex(): bool
    {
        return $this->isWriteIndex;
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
