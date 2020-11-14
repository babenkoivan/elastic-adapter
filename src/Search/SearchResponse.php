<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

final class SearchResponse implements SearchResponseRawInterface
{
    /**
     * @var array
     */
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * @return Hit[]
     */
    public function getHits(): array
    {
        return array_map(static function (array $hit) {
            return new Hit($hit);
        }, $this->response['hits']['hits']);
    }

    public function getHitsTotal(): ?int
    {
        return $this->response['hits']['total']['value'] ?? null;
    }

    public function getSuggestions(): array
    {
        return array_map(static function (array $suggestions) {
            return array_map(static function (array $suggestion) {
                return new Suggestion($suggestion);
            }, $suggestions);
        }, $this->response['suggest'] ?? []);
    }

    public function getAggregations(): array
    {
        return $this->response['aggregations'] ?? [];
    }

    public function getRaw(): array
    {
        return $this->response;
    }
}
