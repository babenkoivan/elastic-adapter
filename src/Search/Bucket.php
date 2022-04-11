<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

final class Bucket implements RawResultInterface
{
    private array $rawBucket;

    public function __construct(array $rawBucket)
    {
        $this->rawBucket = $rawBucket;
    }

    public function docCount(): int
    {
        return $this->rawBucket['doc_count'] ?? 0;
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->rawBucket['key'];
    }

    public function raw(): array
    {
        return $this->rawBucket;
    }
}
