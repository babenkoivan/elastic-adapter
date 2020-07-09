<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

final class Highlight implements SearchResponseRawInterface
{
    /**
     * @var array
     */
    private $highlight;

    public function __construct(array $highlight)
    {
        $this->highlight = $highlight;
    }

    public function getSnippets(string $field): ?array
    {
        return $this->highlight[$field] ?? null;
    }

    public function getRaw(): array
    {
        return $this->highlight;
    }
}
