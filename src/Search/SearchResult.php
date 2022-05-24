<?php declare(strict_types=1);

namespace Elastic\Adapter\Search;

use ArrayAccess;
use Illuminate\Support\Collection;

final class SearchResult implements ArrayAccess
{
    use RawResult;

    /**
     * @return Collection|Hit[]
     */
    public function hits(): Collection
    {
        return collect($this->rawResult['hits']['hits'])->mapInto(Hit::class);
    }

    public function total(): ?int
    {
        return $this->rawResult['hits']['total']['value'] ?? null;
    }

    public function suggestions(): Collection
    {
        return collect($this->rawResult['suggest'] ?? [])->map(
            static fn (array $rawSuggestions) => collect($rawSuggestions)->mapInto(Suggestion::class)
        );
    }

    /**
     * @return Collection|Aggregation[]
     */
    public function aggregations(): Collection
    {
        return collect($this->rawResult['aggregations'] ?? [])->mapInto(Aggregation::class);
    }
}
