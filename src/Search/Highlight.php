<?php declare(strict_types=1);

namespace Elastic\Adapter\Search;

use ArrayAccess;
use Illuminate\Support\Collection;

final class Highlight implements ArrayAccess
{
    use RawResult;

    public function snippets(string $field): Collection
    {
        return collect($this->rawResult[$field] ?? []);
    }
}
