<?php declare(strict_types=1);

namespace ElasticAdapter\Documents;

final class Routing
{
    /**
     * @var array
     */
    private $routing = [];

    public function add(string $documentId, string $value): self
    {
        $this->routing[$documentId] = $value;
        return $this;
    }

    public function has(string $documentId): bool
    {
        return isset($this->routing[$documentId]);
    }

    public function get(string $documentId): ?string
    {
        return $this->routing[$documentId] ?? null;
    }
}
