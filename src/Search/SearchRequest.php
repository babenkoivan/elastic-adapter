<?php
declare(strict_types=1);

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
     * @param  bool|string|array  $source
     * @return self
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

    public function toArray(): array
    {
        $request = [
            'query' => $this->query
        ];

        foreach (['highlight', 'sort', 'from', 'size', 'suggest', 'collapse'] as $property) {
            if (isset($this->$property)) {
                $request[$property] = $this->$property;
            }
        }

        if (isset($this->source)) {
            $request['_source'] = $this->source;
        }

        return $request;
    }
}
