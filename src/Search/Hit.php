<?php declare(strict_types=1);

namespace Elastic\Adapter\Search;

use ArrayAccess;
use Elastic\Adapter\Documents\Document;
use Illuminate\Support\Collection;

final class Hit implements ArrayAccess
{
    use RawResult;

    public function indexName(): string
    {
        return $this->rawResult['_index'];
    }

    public function score(): ?float
    {
        return $this->rawResult['_score'] ?? null;
    }

    public function sort(): ?array
    {
        return $this->rawResult['sort'] ?? null;
    }

    public function document(): Document
    {
        return new Document($this->rawResult['_id'], $this->rawResult['_source'] ?? []);
    }

    public function highlight(): ?Highlight
    {
        return isset($this->rawResult['highlight']) ? new Highlight($this->rawResult['highlight']) : null;
    }

    public function innerHits(): Collection
    {
        return collect($this->rawResult['inner_hits'] ?? [])->map(
            static fn (array $rawHits) => collect($rawHits['hits']['hits'])->mapInto(self::class)
        );
    }

    public function innerHitsTotal(): Collection
    {
        return collect($this->rawResult['inner_hits'] ?? [])->map(
            static fn (array $rawHits) => $rawHits['hits']['total']['value'] ?? null
        );
    }
}
