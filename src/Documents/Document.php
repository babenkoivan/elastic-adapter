<?php declare(strict_types=1);

namespace ElasticAdapter\Documents;

use ElasticAdapter\Support\ArrayableInterface;

final class Document implements ArrayableInterface
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var array
     */
    private $content;

    public function __construct(string $id, array $content)
    {
        $this->id = $id;
        $this->content = $content;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * Get a field's value using "dot" notation.
     *
     * @return mixed field value at key or null if it doesn't exist
     */
    public function getField(string $key)
    {
        $content = $this->getContent();
        if (isset($content[$key])) {
            return $content[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($content) || !array_key_exists($segment, $content)) {
                return null;
            }

            $content = $content[$segment];
        }

        return $content;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'content' => $this->getContent(),
        ];
    }
}
