<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

final class Aggregation implements SearchResponseRawInterface
{
    /**
     * @var array
     */
    private $aggregation;

    public function __construct(array $aggregation)
    {
        $this->aggregation = $aggregation;
    }

    public function getBuckets(): array
    {
        return array_map(static function (array $bucket) {
            return new Bucket($bucket);
        }, $this->aggregation['buckets'] ?? []);
    }

    public function getRaw(): array
    {
        return $this->aggregation;
    }
}
