<?php declare(strict_types=1);

namespace ElasticAdapter\Search;

interface RawResultInterface
{
    public function raw(): array;
}
