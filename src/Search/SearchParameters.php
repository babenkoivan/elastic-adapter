<?php declare(strict_types=1);

namespace Elastic\Adapter\Search;

use Illuminate\Contracts\Support\Arrayable;

final class SearchParameters implements Arrayable
{
    private array $params;

    public function indices(array $indexNames): self
    {
        $this->params['index'] = implode(',', $indexNames);
        return $this;
    }

    public function query(array $query): self
    {
        $this->params['body']['query'] = $query;
        return $this;
    }

    public function highlight(array $highlight): self
    {
        $this->params['body']['highlight'] = $highlight;
        return $this;
    }

    public function sort(array $sort): self
    {
        $this->params['body']['sort'] = $sort;
        return $this;
    }

    public function rescore(array $rescore): self
    {
        $this->params['body']['rescore'] = $rescore;
        return $this;
    }

    public function from(int $from): self
    {
        $this->params['body']['from'] = $from;
        return $this;
    }

    public function size(int $size): self
    {
        $this->params['body']['size'] = $size;
        return $this;
    }

    public function suggest(array $suggest): self
    {
        $this->params['body']['suggest'] = $suggest;
        return $this;
    }

    /**
     * @param bool|string|array $source
     */
    public function source($source): self
    {
        $this->params['body']['_source'] = $source;
        return $this;
    }

    public function collapse(array $collapse): self
    {
        $this->params['body']['collapse'] = $collapse;
        return $this;
    }

    public function aggregations(array $aggregations): self
    {
        $this->params['body']['aggregations'] = $aggregations;
        return $this;
    }

    public function postFilter(array $postFilter): self
    {
        $this->params['body']['post_filter'] = $postFilter;
        return $this;
    }

    /**
     * @param int|bool $trackTotalHits
     */
    public function trackTotalHits($trackTotalHits): self
    {
        $this->params['body']['track_total_hits'] = $trackTotalHits;
        return $this;
    }

    public function indicesBoost(array $indicesBoost): self
    {
        $this->params['body']['indices_boost'] = $indicesBoost;
        return $this;
    }

    public function trackScores(bool $trackScores): self
    {
        $this->params['body']['track_scores'] = $trackScores;
        return $this;
    }

    public function minScore(float $minScore): self
    {
        $this->params['body']['min_score'] = $minScore;
        return $this;
    }

    public function scriptFields(array $scriptFields): self
    {
        $this->params['body']['script_fields'] = $scriptFields;
        return $this;
    }

    public function searchType(string $searchType): self
    {
        $this->params['search_type'] = $searchType;
        return $this;
    }

    public function preference(string $preference): self
    {
        $this->params['preference'] = $preference;
        return $this;
    }

    public function pointInTime(array $pointInTime): self
    {
        $this->params['body']['pit'] = $pointInTime;
        return $this;
    }

    public function searchAfter(array $searchAfter): self
    {
        $this->params['body']['search_after'] = $searchAfter;
        return $this;
    }

    public function routing(array $routing): self
    {
        $this->params['routing'] = implode(',', $routing);
        return $this;
    }

    public function explain(bool $explain): self
    {
        $this->params['body']['explain'] = $explain;
        return $this;
    }

    public function terminateAfter(int $terminateAfter): self
    {
        $this->params['terminate_after'] = $terminateAfter;
        return $this;
    }

    public function requestCache(bool $requestCache): self
    {
        $this->params['request_cache'] = $requestCache;
        return $this;
    }

    public function scroll(string $alive): self
    {
        $this->params['scroll'] = $alive;
        return $this;
    }

    public function toArray(): array
    {
        return $this->params;
    }
}
