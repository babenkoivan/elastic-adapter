<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

interface SearchResponseRawInterface
{
    public function getRaw(): array;
}
