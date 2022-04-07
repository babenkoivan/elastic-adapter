<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

final class Bucket implements RawResponseInterface, \ArrayAccess
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

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->bucket[] = $value;
        } else {
            $this->bucket[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->bucket[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->bucket[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->bucket[$offset]) ? $this->bucket[$offset] : null;
    }
}
