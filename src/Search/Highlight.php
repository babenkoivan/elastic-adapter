<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Support\Collection;

final class Highlight implements RawResponseInterface
{
    /**
     * @var array
     */
    private $highlight;

    public function __construct(array $highlight)
    {
        $this->highlight = $highlight;
    }

    /**
     * @return Collection|string[]
     */
    public function snippets(string $field): Collection
    {
        return collect($this->highlight[$field] ?? []);
    }

    public function raw(): array
    {
        return $this->highlight;
    }
}
