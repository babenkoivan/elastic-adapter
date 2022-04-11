<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ElasticAdapter\Documents\Document;
use Illuminate\Support\Collection;

final class Hit implements RawResultInterface
{
    private array $rawHit;

    public function __construct(array $rawHit)
    {
        $this->rawHit = $rawHit;
    }

    public function indexName(): string
    {
        return $this->rawHit['_index'];
    }

    public function score(): ?float
    {
        return $this->rawHit['_score'];
    }

    public function document(): Document
    {
        return new Document(
            $this->rawHit['_id'],
            $this->rawHit['_source'] ?? []
        );
    }

    public function highlight(): ?Highlight
    {
        return isset($this->rawHit['highlight']) ? new Highlight($this->rawHit['highlight']) : null;
    }

    public function innerHits(): Collection
    {
        $rawInnerHits = $this->rawHit['inner_hits'] ?? [];

        return collect($rawInnerHits)->map(
            static fn (array $rawHits) => collect($rawHits['hits']['hits'])->map(
                static fn (array $rawHit) => new self($rawHit)
            )
        );
    }

    public function innerHitsTotal(string $innerHitsName): ?int
    {
        return $this->rawHit['inner_hits'][$innerHitsName]['hits']['total']['value'] ?? null;
    }

    public function raw(): array
    {
        return $this->rawHit;
    }
}
