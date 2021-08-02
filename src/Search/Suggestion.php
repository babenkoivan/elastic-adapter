<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Support\Collection;

final class Suggestion implements RawResponseInterface
{
    /**
     * @var array
     */
    private $suggestion;

    public function __construct(array $suggestion)
    {
        $this->suggestion = $suggestion;
    }

    public function text(): string
    {
        return $this->suggestion['text'];
    }

    public function offset(): int
    {
        return $this->suggestion['offset'];
    }

    public function length(): int
    {
        return $this->suggestion['length'];
    }

    public function options(): Collection
    {
        return collect($this->suggestion['options']);
    }

    public function raw(): array
    {
        return $this->suggestion;
    }
}
