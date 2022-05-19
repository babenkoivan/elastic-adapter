<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ElasticAdapter\Exceptions\RawResultReadOnlyException;
use ReturnTypeWillChange;

trait RawResult
{
    private array $raw;

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->raw[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->raw[$offset] ?? null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new RawResultReadOnlyException();
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new RawResultReadOnlyException();
    }

    public function raw(): array
    {
        return $this->raw;
    }
}
