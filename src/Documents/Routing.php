<?php declare(strict_types=1);

namespace OpenSearch\Adapter\Documents;

final class Routing
{
    private array $routing = [];

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
