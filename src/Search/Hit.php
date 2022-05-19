<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ArrayAccess;
use ElasticAdapter\Documents\Document;
use Illuminate\Support\Collection;

final class Hit implements ArrayAccess
{
    use RawResult;

    public function __construct(array $rawHit)
    {
        $this->raw = $rawHit;
    }

    public function indexName(): string
    {
        return $this->raw['_index'];
    }

    public function score(): ?float
    {
        return $this->raw['_score'];
    }

    public function document(): Document
    {
        return new Document($this->raw['_id'], $this->raw['_source'] ?? []);
    }

    public function highlight(): ?Highlight
    {
        return isset($this->raw['highlight']) ? new Highlight($this->raw['highlight']) : null;
    }

    public function innerHits(): Collection
    {
        return collect($this->raw['inner_hits'] ?? [])->map(
            static fn (array $rawHits) => collect($rawHits['hits']['hits'])->mapInto(self::class)
        );
    }

    public function innerHitsTotal(): Collection
    {
        return collect($this->raw['inner_hits'] ?? [])->map(
            static fn (array $rawHits) => $rawHits['hits']['total']['value'] ?? null
        );
    }
}
