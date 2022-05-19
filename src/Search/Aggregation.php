<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ArrayAccess;
use Illuminate\Support\Collection;

final class Aggregation implements ArrayAccess
{
    use RawResult;

    public function __construct(array $rawAggregation)
    {
        $this->raw = $rawAggregation;
    }

    /**
     * @return Collection|Bucket[]
     */
    public function buckets(): Collection
    {
        return collect($this->raw['buckets'] ?? [])->mapInto(Bucket::class);
    }
}
