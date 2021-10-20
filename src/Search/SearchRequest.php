<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Contracts\Support\Arrayable;

final class SearchRequest implements Arrayable
{
    /**
     * @var array
     */
    private $request;

    public function __construct(array $query)
    {
        $this->request['query'] = $query;
    }

    public function highlight(array $highlight): self
    {
        $this->request['highlight'] = $highlight;
        return $this;
    }

    public function sort(array $sort): self
    {
        $this->request['sort'] = $sort;
        return $this;
    }

    public function rescore(array $rescore): self
    {
        $this->request['rescore'] = $rescore;
        return $this;
    }

    public function from(int $from): self
    {
        $this->request['from'] = $from;
        return $this;
    }

    public function size(int $size): self
    {
        $this->request['size'] = $size;
        return $this;
    }

    public function suggest(array $suggest): self
    {
        $this->request['suggest'] = $suggest;
        return $this;
    }

    /**
     * @param bool|string|array $source
     */
    public function source($source): self
    {
        $this->request['_source'] = $source;
        return $this;
    }

    public function collapse(array $collapse): self
    {
        $this->request['collapse'] = $collapse;
        return $this;
    }

    public function aggregations(array $aggregations): self
    {
        $this->request['aggregations'] = $aggregations;
        return $this;
    }

    public function postFilter(array $postFilter): self
    {
        $this->request['post_filter'] = $postFilter;
        return $this;
    }

    /**
     * @param int|bool $trackTotalHits
     */
    public function trackTotalHits($trackTotalHits): self
    {
        $this->request['track_total_hits'] = $trackTotalHits;
        return $this;
    }

    public function indicesBoost(array $indicesBoost): self
    {
        $this->request['indices_boost'] = $indicesBoost;
        return $this;
    }

    public function trackScores(bool $trackScores): self
    {
        $this->request['track_scores'] = $trackScores;
        return $this;
    }

    public function minScore(float $minScore): self
    {
        $this->request['min_score'] = $minScore;
        return $this;
    }

    public function scriptFields(array $scriptFields): self
    {
        $this->request['script_fields'] = $scriptFields;
        return $this;
    }

    public function searchAfter(array $searchAfter): self
    {
        $this->request['search_after'] = $searchAfter;
        return $this;
    }

    public function pit(array $pit): self
    {
        $this->request['pit'] = $pit;
        return $this;
    }

    public function toArray(): array
    {
        return $this->request;
    }
}
