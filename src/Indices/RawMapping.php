<?php declare(strict_types=1);

namespace ElasticAdapter\Indices;

use ElasticAdapter\Support\ArrayableInterface;

final class RawMapping implements ArrayableInterface
{
    /**
     * @var array
     */
    private $mapping;

    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function toArray(): array
    {
        return $this->mapping;
    }
}
