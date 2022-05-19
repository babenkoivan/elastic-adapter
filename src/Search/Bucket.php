<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ArrayAccess;

final class Bucket implements ArrayAccess
{
    use RawResult;

    public function __construct(array $rawBucket)
    {
        $this->raw = $rawBucket;
    }

    public function docCount(): int
    {
        return $this->raw['doc_count'] ?? 0;
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->raw['key'];
    }
}
