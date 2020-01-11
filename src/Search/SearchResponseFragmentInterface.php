<?php
declare(strict_types=1);

namespace ElasticAdapter\Search;

interface SearchResponseFragmentInterface
{
    public function getRaw(): array;
}
