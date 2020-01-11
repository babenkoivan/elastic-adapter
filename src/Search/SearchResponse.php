<?php
declare(strict_types=1);

namespace ElasticAdapter\Search;

final class SearchResponse implements SearchResponseFragmentInterface
{
    /**
     * @var array
     */
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

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

    public function getRaw(): array
    {
        return $this->response;
    }
}
