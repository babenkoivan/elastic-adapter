<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ArrayAccess;
use Illuminate\Support\Collection;

final class Suggestion implements ArrayAccess
{
    use RawResult;

    public function __construct(array $rawSuggestion)
    {
        $this->raw = $rawSuggestion;
    }

    public function text(): string
    {
        return $this->raw['text'];
    }

    public function offset(): int
    {
        return $this->raw['offset'];
    }

    public function length(): int
    {
        return $this->raw['length'];
    }

    public function options(): Collection
    {
        return collect($this->raw['options']);
    }
}
