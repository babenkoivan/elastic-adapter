<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

final class Suggestion implements SearchResponseRawInterface
{
    /**
     * @var array
     */
    private $suggestion;

    public function __construct(array $suggestion)
    {
        $this->suggestion = $suggestion;
    }

    public function getText(): string
    {
        return $this->suggestion['text'];
    }

    public function getOffset(): int
    {
        return $this->suggestion['offset'];
    }

    public function getLength(): int
    {
        return $this->suggestion['length'];
    }

    public function getOptions(): array
    {
        return $this->suggestion['options'];
    }

    public function getRaw(): array
    {
        return $this->suggestion;
    }
}
