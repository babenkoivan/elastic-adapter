<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

final class Bucket implements RawResponseInterface
{
    /**
     * @var array
     */
    private $bucket;

    public function __construct(array $bucket)
    {
        $this->bucket = $bucket;
    }

    public function docCount(): int
    {
        return $this->bucket['doc_count'] ?? 0;
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->bucket['key'];
    }

    public function raw(): array
    {
        return $this->bucket;
    }
}
