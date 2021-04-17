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
     * @var string|null
     */
    private $routing;
    /**
     * @var array
     */
    private $content;

    public function __construct(string $id, array $content, ?string $routing = null)
    {
        $this->id = $id;
        $this->routing = $routing;
        $this->content = $content;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRouting(): ?string
    {
        return $this->routing;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'content' => $this->getContent(),
        ];
    }
}
