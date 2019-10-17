<?php
declare(strict_types=1);

namespace ElasticAdaptor\Support;

interface ArrayableInterface
{
    /**
     * @return array
     */
    public function toArray(): array;
}
