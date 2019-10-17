<?php
declare(strict_types=1);

namespace ElasticAdaptor\Documents;

final class Document
{
    private const DEFAULT_TYPE = '_doc';

    /**
     * @return string
     */
    public static function getType(): string
    {
        return static::DEFAULT_TYPE;
    }
}
