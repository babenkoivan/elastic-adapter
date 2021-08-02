<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

interface RawResponseInterface
{
    public function raw(): array;
}
