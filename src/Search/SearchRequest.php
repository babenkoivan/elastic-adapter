<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use ElasticAdapter\Support\ArrayableInterface;

final class SearchRequest implements ArrayableInterface
{
    /**
     * @var array
     */
    private $query;
    /**
     * @var array|null
     */
    private $highlight;
    /**
     * @var array|null
     */
    private $sort;
    /**
     * @var int|null
     */
    private $from;
    /**
     * @var int|null
     */
    private $size;
    /**
     * @var array|null
     */
    private $suggest;
    /**
     * @var bool|string|array|null
     */
    private $source;
    /**
     * @var array|null
     */
    private $collapse;
    /**
     * @var array|null
     */
    private $aggregations;
    /**
     * @var array|null
     */
    private $postFilter;
    /**
     * @var int|bool|null
     */
    private $trackTotalHits;
    /**
     * @var array|null
     */
    private $indicesBoost;
    /**
     * @var bool|null
     */
    private $trackScores;
    /**
     * @var array|null
     */
    private $scriptFields;

    public function __construct(array $query)
    {
        $this->query = $query;
    }

    public function setHighlight(array $highlight): self
    {
        $this->highlight = $highlight;
        return $this;
    }

    public function setSort(array $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    public function setFrom(int $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function setSuggest(array $suggest): self
    {
        $this->suggest = $suggest;
        return $this;
    }

    /**
     * @param bool|string|array $source
     */
    public function setSource($source): self
    {
        $this->source = $source;
        return $this;
    }

    public function setCollapse(array $collapse): self
    {
        $this->collapse = $collapse;
        return $this;
    }

    public function setAggregations(array $aggregations): self
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    public function setPostFilter(array $postFilter): self
    {
        $this->postFilter = $postFilter;
        return $this;
    }

    /**
     * @param int|bool $trackTotalHits
     */
    public function setTrackTotalHits($trackTotalHits): self
    {
        $this->trackTotalHits = $trackTotalHits;
        return $this;
    }

    public function setIndicesBoost(array $indicesBoost): self
    {
        $this->indicesBoost = $indicesBoost;
        return $this;
    }

    public function setTrackScores(bool $trackScores): self
    {
        $this->trackScores = $trackScores;
        return $this;
    }

    public function setScriptFields(array $scriptFields): self
    {
        $this->scriptFields = $scriptFields;
        return $this;
    }

    public function toArray(): array
    {
        $request = [
            'query' => $this->query,
        ];

        foreach ([
            'highlight' => 'highlight',
            'sort' => 'sort',
            'from' => 'from',
            'size' => 'size',
            'suggest' => 'suggest',
            'collapse' => 'collapse',
            'aggregations' => 'aggregations',
            'source' => '_source',
            'postFilter' => 'post_filter',
            'trackTotalHits' => 'track_total_hits',
            'indicesBoost' => 'indices_boost',
            'trackScores' => 'track_scores',
            'scriptFields' => 'script_fields',
        ] as $property => $requestParameter) {
            if (isset($this->$property)) {
                $request[$requestParameter] = $this->$property;
            }
        }

        return $request;
    }
}
