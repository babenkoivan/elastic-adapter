<?php declare(strict_types=1);

namespace Elastic\Adapter\Indices;

final class Index
{
    private string $name;
    private ?Mapping $mapping;
    private ?Settings $settings;

    public function __construct(string $name, Mapping $mapping = null, Settings $settings = null)
    {
        $this->name = $name;
        $this->mapping = $mapping;
        $this->settings = $settings;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function mapping(): ?Mapping
    {
        return $this->mapping;
    }

    public function settings(): ?Settings
    {
        return $this->settings;
    }
}
