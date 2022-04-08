<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

use Illuminate\Support\Collection;

final class Suggestion implements RawInterface
{
    private array $rawSuggestion;

    public function __construct(array $rawSuggestion)
    {
        $this->rawSuggestion = $rawSuggestion;
    }

    public function text(): string
    {
        return $this->rawSuggestion['text'];
    }

    public function offset(): int
    {
        return $this->rawSuggestion['offset'];
    }

    public function length(): int
    {
        return $this->rawSuggestion['length'];
    }

    public function options(): Collection
    {
        return collect($this->rawSuggestion['options']);
    }

    public function raw(): array
    {
        return $this->rawSuggestion;
    }
}
