<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ArrayAccess;
use Illuminate\Support\Collection;

final class SearchResult implements ArrayAccess
{
    use RawResult;

    public function __construct(array $rawResult)
    {
        $this->raw = $rawResult;
    }

    /**
     * @return Collection|Hit[]
     */
    public function hits(): Collection
    {
        return collect($this->raw['hits']['hits'])->mapInto(Hit::class);
    }

    public function total(): ?int
    {
        return $this->raw['hits']['total']['value'] ?? null;
    }

    public function suggestions(): Collection
    {
        return collect($this->raw['suggest'] ?? [])->map(
            static fn (array $rawSuggestions) => collect($rawSuggestions)->mapInto(Suggestion::class)
        );
    }

    /**
     * @return Collection|Aggregation[]
     */
    public function aggregations(): Collection
    {
        return collect($this->raw['aggregations'] ?? [])->mapInto(Aggregation::class);
    }
}
