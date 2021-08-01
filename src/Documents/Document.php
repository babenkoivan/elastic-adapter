<?php declare(strict_types=1);

namespace ElasticAdapter\Documents;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

final class Document implements Arrayable
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

    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function content(string $key = null)
    {
        return Arr::get($this->content, $key);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
        ];
    }
}
