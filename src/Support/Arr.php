<?php declare(strict_types=1);

namespace ElasticAdapter\Support;

final class Arr
{
    /**
     * Get an item from an array using "dot" notation.
     *
     * @return mixed
     */
    public static function get(array $array, string $key)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!array_key_exists($segment, $array)) {
                return null;
            }

            $array = $array[$segment];
        }

        return $array;
    }
}
