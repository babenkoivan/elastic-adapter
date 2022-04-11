<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Support\Collection;

final class Highlight implements RawResultInterface
{
    private array $rawHighlight;

    public function __construct(array $rawHighlight)
    {
        $this->rawHighlight = $rawHighlight;
    }

    /**
     * @return Collection|string[]
     */
    public function snippets(string $field): Collection
    {
        return collect($this->rawHighlight[$field] ?? []);
    }

    public function raw(): array
    {
        return $this->rawHighlight;
    }
}
