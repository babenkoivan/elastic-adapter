<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Support\Collection;

final class SearchResult implements RawResultInterface
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
        return collect($rawHits)->mapInto(Hit::class);
    }

    public function total(): ?int
    {
        return $this->rawResult['hits']['total']['value'] ?? null;
    }

    public function suggestions(): Collection
    {
        $rawSuggest = $this->rawResult['suggest'] ?? [];

        return collect($rawSuggest)->map(
            static fn (array $rawSuggestions) => collect($rawSuggestions)->mapInto(Suggestion::class)
        );
    }

    /**
     * @return Collection|Aggregation[]
     */
    public function aggregations(): Collection
    {
        $rawAggregations = $this->rawResult['aggregations'] ?? [];
        return collect($rawAggregations)->mapInto(Aggregation::class);
    }

    public function raw(): array
    {
        return $this->rawResult;
    }
}
