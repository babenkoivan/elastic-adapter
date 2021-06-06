<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

final class Bucket implements SearchResponseRawInterface
{
    /**
     * @var array
     */
    private $bucket;

    public function __construct(array $bucket)
    {
        $this->bucket = $bucket;
    }

    public function getDocCount(): int
    {
        return $this->bucket['doc_count'] ?? 0;
    }

    public function getKey(): string
    {
        return $this->bucket['key'];
    }

    public function getRaw(): array
    {
        return $this->bucket;
    }
}
