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
     * @var array
     */
    private $highlight;
    /**
     * @var array
     */
    private $sort;
    /**
     * @var int
     */
    private $from;
    /**
     * @var int
     */
    private $size;
    /**
     * @var array
     */
    private $suggest;

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

    public function toArray(): array
    {
        $request = [
            'query' => $this->query
        ];

        foreach (['highlight', 'sort', 'from', 'size', 'suggest'] as $property) {
            if (isset($this->$property)) {
                $request[$property] = $this->$property;
            }
        }

        return $request;
    }
}
