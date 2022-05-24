<?php declare(strict_types=1);

namespace Elastic\Adapter\Search;

use ArrayAccess;
use Illuminate\Support\Collection;

final class Aggregation implements ArrayAccess
{
    use RawResult;

    /**
     * @return Collection|Bucket[]
     */
    public function buckets(): Collection
    {
        return collect($this->rawResult['buckets'] ?? [])->mapInto(Bucket::class);
    }
}
