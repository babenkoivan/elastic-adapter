<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Support\Collection;

final class SearchResult implements RawInterface
{
    private array $rawResult;

    public function __construct(array $rawResult)
    {
        $this->rawResult = $rawResult;
    }

    /**
     * @return Collection|Hit[]
     */
    public function hits(): Collection
    {
        $rawHits = $this->rawResult['hits']['hits'];
        return collect($rawHits)->map(static fn (array $hit) => new Hit($hit));
    }

    public function total(): ?int
    {
        return $this->rawResult['hits']['total']['value'] ?? null;
    }

    public function suggestions(): Collection
    {
        $rawSuggest = $this->rawResult['suggest'] ?? [];

        return collect($rawSuggest)->map(
            static fn (array $suggestions) => collect($suggestions)->map(
                static fn (array $suggestion) => new Suggestion($suggestion)
            )
        );
    }

    /**
     * @return Collection|Aggregation[]
     */
    public function aggregations(): Collection
    {
        $rawAggregations = $this->rawResult['aggregations'] ?? [];
        return collect($rawAggregations)->map(static fn (array $aggregation) => new Aggregation($aggregation));
    }

    public function raw(): array
    {
        return $this->rawResult;
    }
}
