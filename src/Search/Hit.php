<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ElasticAdapter\Documents\Document;

final class Hit implements SearchResponseRawInterface
{
    /**
     * @var array
     */
    private $hit;

    public function __construct(array $hit)
    {
        $this->hit = $hit;
    }

    public function getIndexName(): string
    {
        return $this->hit['_index'];
    }

    public function getScore(): ?float
    {
        return $this->hit['_score'];
    }

    public function getDocument(): Document
    {
        return new Document(
            $this->hit['_id'],
            $this->hit['_source'] ?? []
        );
    }

    public function getHighlight(): ?Highlight
    {
        return isset($this->hit['highlight']) ?
            new Highlight($this->hit['highlight']) : null;
    }

    public function getRaw(): array
    {
        return $this->hit;
    }
}
