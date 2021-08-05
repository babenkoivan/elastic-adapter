<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Support\Collection;

final class SearchResponse implements RawResponseInterface
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
     * @return Collection|Hit[]
     */
    public function hits(): Collection
    {
        $hits = $this->response['hits']['hits'];

        return collect($hits)->map(static function (array $hit) {
            return new Hit($hit);
        });
    }

    public function total(): ?int
    {
        return $this->response['hits']['total']['value'] ?? null;
    }

    public function suggestions(): Collection
    {
        $suggest = $this->response['suggest'] ?? [];

        return collect($suggest)->map(static function (array $suggestions) {
            return collect($suggestions)->map(static function (array $suggestion) {
                return new Suggestion($suggestion);
            });
        });
    }

    /**
     * @return Collection|Aggregation[]
     */
    public function aggregations(): Collection
    {
        $aggregations = $this->response['aggregations'] ?? [];

        return collect($aggregations)->map(static function (array $aggregation) {
            return new Aggregation($aggregation);
        });
    }

    public function raw(): array
    {
        return $this->response;
    }
}
