<?php declare(strict_types=1);

namespace Elastic\Adapter\Search;

use ArrayAccess;
use Illuminate\Support\Collection;

final class Suggestion implements ArrayAccess
{
    use RawResult;

    public function text(): string
    {
        return $this->rawResult['text'];
    }

    public function offset(): int
    {
        return $this->rawResult['offset'];
    }

    public function length(): int
    {
        return $this->rawResult['length'];
    }

    public function options(): Collection
    {
        return collect($this->rawResult['options']);
    }
}
