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
        $content = $this->content;
        if (array_key_exists('routing', $content)) {
            unset($content['routing']);
        }
        return $content;
    }

    public function getParams(): array
    {
        $params = [];
        $params['routing'] = array_key_exists('routing', $this->content) ? $this->content['routing'] : null;
        return $params;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'content' => $this->getContent(),
            'routing' => $this->getParams()['routing'],
        ];
    }
}
