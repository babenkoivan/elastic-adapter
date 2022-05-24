<?php declare(strict_types=1);

namespace Elastic\Adapter\Search;

use Elastic\Adapter\Exceptions\RawResultReadOnlyException;
use ReturnTypeWillChange;

trait RawResult
{
    private array $rawResult;

    public function __construct(array $rawResult)
    {
        $this->rawResult = $rawResult;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->rawResult[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->rawResult[$offset] ?? null;
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
        return $this->rawResult;
    }
}
