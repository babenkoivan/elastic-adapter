<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Support\Collection;

final class Aggregation implements RawResponseInterface
{
    private array $aggregation;

    public function __construct(array $aggregation)
    {
        $this->aggregation = $aggregation;
    }

    /**
     * @return Collection|Bucket[]
     */
    public function buckets(): Collection
    {
        $buckets = $this->aggregation['buckets'] ?? [];

        return collect($buckets)->map(static function (array $bucket) {
            return new Bucket($bucket);
        });
    }

    public function raw(): array
    {
        return $this->aggregation;
    }
}
