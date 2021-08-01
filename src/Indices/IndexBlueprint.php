<?php declare(strict_types=1);

namespace ElasticAdapter\Indices;

final class IndexBlueprint
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Mapping|null
     */
    private $mapping;
    /**
     * @var Settings|null
     */
    private $settings;

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
