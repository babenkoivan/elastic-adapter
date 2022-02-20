<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Contracts\Support\Arrayable;

final class SearchRequest implements Arrayable
{
    /**
     * @var array
     */
    private $request;

    public function __construct(?array $query = null)
    {
        if (isset($query)) {
            $this->request['body']['query'] = $query;
        }
    }

    public function highlight(array $highlight): self
    {
        $this->request['body']['highlight'] = $highlight;
        return $this;
    }

    public function sort(array $sort): self
    {
        $this->request['body']['sort'] = $sort;
        return $this;
    }

    public function rescore(array $rescore): self
    {
        $this->request['body']['rescore'] = $rescore;
        return $this;
    }

    public function from(int $from): self
    {
        $this->request['body']['from'] = $from;
        return $this;
    }

    public function size(int $size): self
    {
        $this->request['body']['size'] = $size;
        return $this;
    }

    public function suggest(array $suggest): self
    {
        $this->request['body']['suggest'] = $suggest;
        return $this;
    }

    /**
     * @param bool|string|array $source
     */
    public function source($source): self
    {
        $this->request['body']['_source'] = $source;
        return $this;
    }

    public function collapse(array $collapse): self
    {
        $this->request['body']['collapse'] = $collapse;
        return $this;
    }

    public function aggregations(array $aggregations): self
    {
        $this->request['body']['aggregations'] = $aggregations;
        return $this;
    }

    public function postFilter(array $postFilter): self
    {
        $this->request['body']['post_filter'] = $postFilter;
        return $this;
    }

    /**
     * @param int|bool $trackTotalHits
     */
    public function trackTotalHits($trackTotalHits): self
    {
        $this->request['body']['track_total_hits'] = $trackTotalHits;
        return $this;
    }

    public function indicesBoost(array $indicesBoost): self
    {
        $this->request['body']['indices_boost'] = $indicesBoost;
        return $this;
    }

    public function trackScores(bool $trackScores): self
    {
        $this->request['body']['track_scores'] = $trackScores;
        return $this;
    }

    public function minScore(float $minScore): self
    {
        $this->request['body']['min_score'] = $minScore;
        return $this;
    }

    public function scriptFields(array $scriptFields): self
    {
        $this->request['body']['script_fields'] = $scriptFields;
        return $this;
    }

    public function searchType(string $searchType): self
    {
        $this->request['search_type'] = $searchType;
        return $this;
    }

    public function preference(string $preference): self
    {
        $this->request['preference'] = $preference;
        return $this;
    }

    public function toArray(): array
    {
        return $this->request;
    }
}
