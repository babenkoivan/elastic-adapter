<?php declare(strict_types=1);

namespace Elastic\Adapter\Search;

use ArrayAccess;

final class Bucket implements ArrayAccess
{
    use RawResult;

    public function docCount(): int
    {
        return $this->rawResult['doc_count'] ?? 0;
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->rawResult['key'];
    }
}
