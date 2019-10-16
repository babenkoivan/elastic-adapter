<?php
declare(strict_types=1);

namespace ElasticAdaptor\Indices;

final class Index
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

    /**
     * @param string $name
     * @param Mapping|null $mapping
     * @param Settings|null $settings
     */
    public function __construct(string $name, Mapping $mapping = null, Settings $settings = null)
    {
        $this->name = $name;
        $this->mapping = $mapping;
        $this->settings = $settings;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Mapping|null
     */
    public function getMapping(): ?Mapping
    {
        return $this->mapping;
    }

    /**
     * @return Settings|null
     */
    public function getSettings(): ?Settings
    {
        return $this->settings;
    }
}
