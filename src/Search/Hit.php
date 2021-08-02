<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ElasticAdapter\Documents\Document;

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

    public function raw(): array
    {
        return $this->hit;
    }
}
