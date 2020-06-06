<?php
declare(strict_types=1);

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
        return array_map(function (array $hit) {
            return new Hit($hit);
        }, $this->response['hits']['hits']);
    }

    public function getHitsTotal(): int
    {
        return $this->response['hits']['total']['value'];
    }

    public function getSuggestions(): array
    {
        return array_map(function (array $suggestions) {
            return array_map(function (array $suggestion) {
                return new Suggestion($suggestion);
            }, $suggestions);
        }, $this->response['suggest'] ?? []);
    }

    public function getRaw(): array
    {
        return $this->response;
    }
}
