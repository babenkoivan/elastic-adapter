<?php
declare(strict_types=1);

namespace ElasticAdaptor\Support;

final class Str
{
    /**
     * @param string $string
     * @return string
     */
    public static function toSnakeCase(string $string): string
    {
        return strtolower(preg_replace(
            ['/[^\w]/', '/([a-z0-9])([A-Z])/'],
            ['_', '$1_$2'],
            $string
        ));
    }
}
