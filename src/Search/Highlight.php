<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ArrayAccess;
use Illuminate\Support\Collection;

final class Highlight implements ArrayAccess
{
    use RawResult;

    public function __construct(array $rawHighlight)
    {
        $this->raw = $rawHighlight;
    }

    /**
     * @return Collection|string[]
     */
    public function snippets(string $field): Collection
    {
        return collect($this->raw[$field] ?? []);
    }
}
