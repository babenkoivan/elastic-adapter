<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ElasticAdapter\Documents\Document;
use Illuminate\Support\Collection;

final class Hit implements RawResponseInterface
{
    /**
     * @var array
     */
    private $hit;

    public function __construct(array $hit)
    {
        $this->hit = $hit;
    }

    public function indexName(): string
    {
        return $this->hit['_index'];
    }

    public function score(): ?float
    {
        return $this->hit['_score'];
    }

    public function document(): Document
    {
        return new Document(
            $this->hit['_id'],
            $this->hit['_source'] ?? []
        );
    }

    public function highlight(): ?Highlight
    {
        return isset($this->hit['highlight']) ?
            new Highlight($this->hit['highlight']) : null;
    }

    public function innerHits(): Collection
    {
        $innerHits = $this->hit['inner_hits'] ?? [];

        return collect($innerHits)->map(static function (array $hits) {
            return collect($hits['hits']['hits'])->map(static function (array $hit) {
                return new self($hit);
            });
        });
    }

    public function raw(): array
    {
        return $this->hit;
    }
}
