<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Support\Collection;

final class Aggregation implements RawResultInterface
{
    private array $rawAggregation;

    public function __construct(array $rawAggregation)
    {
        $this->rawAggregation = $rawAggregation;
    }

    /**
     * @return Collection|Bucket[]
     */
    public function buckets(): Collection
    {
        $rawBuckets = $this->rawAggregation['buckets'] ?? [];
        return collect($rawBuckets)->map(static fn (array $rawBucket) => new Bucket($rawBucket));
    }

    public function raw(): array
    {
        return $this->rawAggregation;
    }
}
